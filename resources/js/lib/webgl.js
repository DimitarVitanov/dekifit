import { Program } from 'ogl';

// Shared by the WebGL backgrounds (Lightfall, Particles, Orb). They look and move exactly as
// designed; these helpers only stop them blocking the page or burning CPU/GPU unseen.

// Without a GPU (headless Chrome such as Lighthouse/PageSpeed, VMs, blocklisted drivers) WebGL is
// drawn on the CPU and a full-screen shader freezes the page for seconds, so the effects stay off
// there (see hasGpu below). Real phones and computers all have one and animate as designed.

// A canvas with a GPU-backed WebGL context already created (ogl's Renderer picks it up when
// given the canvas), or null if the browser would only give a CPU one. Defaults match ogl's.
export function hardwareCanvas({
    alpha = false,
    depth = true,
    stencil = false,
    antialias = false,
    premultipliedAlpha = false,
    preserveDrawingBuffer = false,
    powerPreference = 'default',
} = {}) {
    const canvas = document.createElement('canvas');
    const attributes = { alpha, depth, stencil, antialias, premultipliedAlpha, preserveDrawingBuffer, powerPreference, failIfMajorPerformanceCaveat: true };
    return canvas.getContext('webgl2', attributes) || canvas.getContext('webgl', attributes) ? canvas : null;
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

const SOFTWARE = /swiftshader|llvmpipe|softpipe|software/i;
let gpuCheck = null;

// Resolves true when WebGL runs on a real GPU. The question is asked from a worker: any WebGL
// query waits on the GPU process, and when that is software-rendering it stalls for seconds,
// which must not happen on the page's main thread.
export function hasGpu() {
    return (gpuCheck ??= new Promise(resolve => {
        const onMainThread = () => {
            try {
                const gl = document.createElement('canvas').getContext('webgl', { failIfMajorPerformanceCaveat: true });
                if (!gl) return resolve(false);
                const info = gl.getExtension('WEBGL_debug_renderer_info');
                resolve(!SOFTWARE.test(String(gl.getParameter(info ? info.UNMASKED_RENDERER_WEBGL : gl.RENDERER))));
                gl.getExtension('WEBGL_lose_context')?.loseContext();
            } catch {
                resolve(false);
            }
        };
        if (typeof Worker === 'undefined' || typeof OffscreenCanvas === 'undefined') return onMainThread();
        // Posts the renderer name, 'software' (only a CPU context), or 'unsupported' (no WebGL in workers).
        const source = `
            const canvas = new OffscreenCanvas(1, 1);
            const gl = canvas.getContext('webgl', { failIfMajorPerformanceCaveat: true });
            if (gl) {
                const info = gl.getExtension('WEBGL_debug_renderer_info');
                postMessage(String(gl.getParameter(info ? info.UNMASKED_RENDERER_WEBGL : gl.RENDERER)));
                gl.getExtension('WEBGL_lose_context')?.loseContext();
            } else {
                postMessage(new OffscreenCanvas(1, 1).getContext('webgl') ? 'software' : 'unsupported');
            }`;
        try {
            const url = URL.createObjectURL(new Blob([source], { type: 'text/javascript' }));
            const worker = new Worker(url);
            const done = () => {
                worker.terminate();
                URL.revokeObjectURL(url);
            };
            worker.onmessage = ({ data }) => {
                done();
                if (data === 'unsupported') onMainThread();
                else resolve(data !== 'software' && !SOFTWARE.test(data));
            };
            worker.onerror = () => {
                done();
                onMainThread();
            };
        } catch {
            onMainThread();
        }
    }));
}

// Runs `fn` once, the first time `el` comes within a screen's height of the viewport and only if
// WebGL runs on a GPU, so effects further down the page don't set up during load and machines
// without a GPU never start them. Returns a cancel function.
export function onceNear(el, fn) {
    let done = false;
    let cancelled = false;
    const stop = watchVisibility(el, visible => {
        if (!visible || done) return;
        done = true;
        stop?.();
        hasGpu().then(gpu => {
            if (gpu && !cancelled) fn();
        });
    }, '100% 0px');
    if (done) stop();
    return () => {
        cancelled = done = true;
        stop();
    };
}

// Runs `fn` after the browser has painted the current frame, so a shader compile never
// delays the page's first paint.
export function afterPaint(fn) {
    let cancelled = false;
    requestAnimationFrame(() => setTimeout(() => { if (!cancelled) fn(); }, 0));
    return () => { cancelled = true; };
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
