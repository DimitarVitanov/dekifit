import { Program } from 'ogl';

// Shared by the WebGL backgrounds (Lightfall, Particles, Orb). They look and move exactly as
// designed; these helpers only stop them blocking the page or burning CPU/GPU unseen.

// True when WebGL is drawn by the CPU instead of a GPU (SwiftShader/llvmpipe): headless Chrome
// such as Lighthouse/PageSpeed, VMs, blocklisted drivers. Animating a full-screen shader there
// freezes the page, so those browsers get one still frame instead of a running animation.
export function isSoftwareRenderer(gl) {
    try {
        const info = gl.getExtension('WEBGL_debug_renderer_info');
        const name = info ? gl.getParameter(info.UNMASKED_RENDERER_WEBGL) : gl.getParameter(gl.RENDERER);
        return /swiftshader|llvmpipe|softpipe|software/i.test(String(name));
    } catch {
        return false;
    }
}

// Calls onChange(true/false) whenever `el` comes near/leaves the viewport or the tab is
// shown/hidden. Returns a function that stops watching.
export function watchVisibility(el, onChange, rootMargin = '150px') {
    let inView = typeof IntersectionObserver === 'undefined';
    let last = null;
    const update = () => {
        const visible = inView && !document.hidden;
        if (visible !== last) onChange((last = visible));
    };
    const observer = inView ? null : new IntersectionObserver(([entry]) => {
        inView = entry.isIntersecting;
        update();
    }, { rootMargin });
    observer?.observe(el);
    document.addEventListener('visibilitychange', update);
    update();
    return () => {
        observer?.disconnect();
        document.removeEventListener('visibilitychange', update);
    };
}

// Runs `fn` after the browser has painted the current frame, so a shader compile never
// delays the page's first paint.
export function afterPaint(fn) {
    let cancelled = false;
    requestAnimationFrame(() => setTimeout(() => { if (!cancelled) fn(); }, 0));
    return () => { cancelled = true; };
}

// Resolves once the page has loaded, `delay` ms have passed and the browser is idle, and `el` is
// on (or near) screen. For work that would freeze the page, like compiling a big shader without
// KHR_parallel_shader_compile.
export function whenSettledAndVisible(el, delay = 3000) {
    const loaded = document.readyState === 'complete'
        ? Promise.resolve()
        : new Promise(resolve => window.addEventListener('load', resolve, { once: true }));
    const settled = loaded
        .then(() => new Promise(resolve => setTimeout(resolve, delay)))
        .then(() => new Promise(resolve => (window.requestIdleCallback ?? setTimeout)(resolve, { timeout: 2000 })));
    const visible = new Promise(resolve => {
        let seen = false;
        const stop = watchVisibility(el, isVisible => {
            if (isVisible && !seen) {
                seen = true;
                resolve();
                stop?.();
            }
        });
        if (seen) stop();
    });
    return Promise.all([settled, visible]);
}

// An ogl Program that compiles without freezing the page. ogl reads the compile log and link
// status straight after compiling, which forces the browser to finish a large shader (Lightfall's
// raymarcher takes seconds on phones) on the main thread. With KHR_parallel_shader_compile the
// GPU process compiles in the background instead. Draw only after `await program.ready`.
export class ParallelProgram extends Program {
    setShaders({ vertex, fragment }) {
        const gl = this.gl;
        const ext = gl.getExtension('KHR_parallel_shader_compile');
        if (!ext) {
            super.setShaders({ vertex, fragment });
            this.ready = Promise.resolve();
            return;
        }
        gl.shaderSource(this.vertexShader, vertex);
        gl.compileShader(this.vertexShader);
        gl.shaderSource(this.fragmentShader, fragment);
        gl.compileShader(this.fragmentShader);
        gl.linkProgram(this.program);
        this.ready = new Promise(resolve => {
            const poll = () => {
                if (!gl.getProgramParameter(this.program, ext.COMPLETION_STATUS_KHR)) return setTimeout(poll, 32);
                this.locate();
                resolve();
            };
            poll();
        });
    }

    // The rest of ogl 1.0.11's setShaders (core/Program.js), run once linking has finished.
    locate() {
        const gl = this.gl;
        if (!gl.getProgramParameter(this.program, gl.LINK_STATUS)) {
            return console.warn(gl.getProgramInfoLog(this.program));
        }

        this.uniformLocations = new Map();
        const numUniforms = gl.getProgramParameter(this.program, gl.ACTIVE_UNIFORMS);
        for (let uIndex = 0; uIndex < numUniforms; uIndex++) {
            const uniform = gl.getActiveUniform(this.program, uIndex);
            this.uniformLocations.set(uniform, gl.getUniformLocation(this.program, uniform.name));
            const split = uniform.name.match(/(\w+)/g);
            uniform.uniformName = split[0];
            uniform.nameComponents = split.slice(1);
        }

        this.attributeLocations = new Map();
        const locations = [];
        const numAttribs = gl.getProgramParameter(this.program, gl.ACTIVE_ATTRIBUTES);
        for (let aIndex = 0; aIndex < numAttribs; aIndex++) {
            const attribute = gl.getActiveAttrib(this.program, aIndex);
            const location = gl.getAttribLocation(this.program, attribute.name);
            if (location === -1) continue;
            locations[location] = attribute.name;
            this.attributeLocations.set(attribute, location);
        }
        this.attributeOrder = locations.join('');
    }
}
