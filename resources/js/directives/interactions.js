// Subtle, reduced-motion-aware pointer interactions used across the site.
// Registered globally in app.js as v-tilt and v-magnetic.

const EASE = 'cubic-bezier(0.22, 1, 0.36, 1)';

function prefersReducedMotion() {
    return (
        typeof window !== 'undefined' &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches
    );
}

// A pointer-capable device (skip on touch — nothing to hover).
function hasFinePointer() {
    return typeof window !== 'undefined' && window.matchMedia('(hover: hover) and (pointer: fine)').matches;
}

// v-tilt — the element leans a few degrees toward the cursor, then settles back.
// Usage: v-tilt  (default 6°)  or  v-tilt="10"  (custom max degrees)
export const tilt = {
    mounted(el, binding) {
        if (prefersReducedMotion() || !hasFinePointer()) return;

        const max = typeof binding.value === 'number' ? binding.value : 6;
        let raf = null;

        el.style.transition = `transform 220ms ${EASE}`;
        el.style.willChange = 'transform';

        const onMove = e => {
            const rect = el.getBoundingClientRect();
            const px = (e.clientX - rect.left) / rect.width; // 0..1
            const py = (e.clientY - rect.top) / rect.height; // 0..1
            const rotateX = (0.5 - py) * max * 2;
            const rotateY = (px - 0.5) * max * 2;
            if (raf) cancelAnimationFrame(raf);
            raf = requestAnimationFrame(() => {
                el.style.transform = `perspective(900px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg)`;
            });
        };

        const reset = () => {
            if (raf) cancelAnimationFrame(raf);
            el.style.transform = 'perspective(900px) rotateX(0deg) rotateY(0deg)';
        };

        el.addEventListener('mousemove', onMove);
        el.addEventListener('mouseleave', reset);
        el.__interactionCleanup = () => {
            el.removeEventListener('mousemove', onMove);
            el.removeEventListener('mouseleave', reset);
        };
    },
    unmounted(el) {
        el.__interactionCleanup?.();
    }
};

// v-magnetic — the element drifts toward the cursor while hovered, springs back on leave.
// Usage: v-magnetic  (default strength 0.25)  or  v-magnetic="0.4"
export const magnetic = {
    mounted(el, binding) {
        if (prefersReducedMotion() || !hasFinePointer()) return;

        const strength = typeof binding.value === 'number' ? binding.value : 0.25;
        let raf = null;

        el.style.transition = `transform 220ms ${EASE}`;
        el.style.willChange = 'transform';

        const onMove = e => {
            const rect = el.getBoundingClientRect();
            const mx = e.clientX - (rect.left + rect.width / 2);
            const my = e.clientY - (rect.top + rect.height / 2);
            if (raf) cancelAnimationFrame(raf);
            raf = requestAnimationFrame(() => {
                el.style.transform = `translate(${(mx * strength).toFixed(1)}px, ${(my * strength).toFixed(1)}px)`;
            });
        };

        const reset = () => {
            if (raf) cancelAnimationFrame(raf);
            el.style.transform = 'translate(0px, 0px)';
        };

        el.addEventListener('mousemove', onMove);
        el.addEventListener('mouseleave', reset);
        el.__interactionCleanup = () => {
            el.removeEventListener('mousemove', onMove);
            el.removeEventListener('mouseleave', reset);
        };
    },
    unmounted(el) {
        el.__interactionCleanup?.();
    }
};

export function registerInteractions(app) {
    app.directive('tilt', tilt);
    app.directive('magnetic', magnetic);
}
