import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import Lenis from 'lenis';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { registerInteractions } from './directives/interactions';
import '../css/app.css';

gsap.registerPlugin(ScrollTrigger);

const prefersReducedMotion =
    typeof window !== 'undefined' &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const appName = import.meta.env.VITE_APP_NAME || 'DekiFit';

createInertiaApp({
    title: (title) => title ? (title.includes('DekiFit') ? title : `${title} — DekiFit`) : 'DekiFit — Train. Focus. Transform.',
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) }).use(plugin);
        registerInteractions(app);
        return app.mount(el);
    },
    progress: {
        color: '#F2B90C',
    },
});

// Skip smooth-scroll hijacking when the visitor asked for reduced motion —
// native scrolling stays, and ScrollTrigger falls back to the window scroller.
if (!prefersReducedMotion) {
    const lenis = new Lenis({
        duration: 1.2,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        smoothWheel: true,
    });

    // Keep GSAP ScrollTrigger in sync with Lenis' virtual scroll position.
    // Without this, every below-the-fold scroll-triggered animation gets
    // stranded in its initial (broken/hidden) state.
    lenis.on('scroll', ScrollTrigger.update);

    gsap.ticker.add((time) => {
        lenis.raf(time * 1000);
    });
    gsap.ticker.lagSmoothing(0);

    // Recalculate trigger positions once fonts/images have settled.
    window.addEventListener('load', () => ScrollTrigger.refresh());
}
