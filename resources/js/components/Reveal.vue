<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

// Lightweight scroll-reveal wrapper: fades + slides its content in the first time
// it scrolls into view. Uses IntersectionObserver (independent of GSAP/Lenis) so
// content can never be stranded hidden. Extra classes/attrs fall through to the root.
const props = defineProps({
    as: { type: String, default: 'div' },
    y: { type: Number, default: 24 },
    delay: { type: Number, default: 0 },
    duration: { type: Number, default: 700 },
    threshold: { type: Number, default: 0.15 },
    once: { type: Boolean, default: true }
});

const root = ref(null);
const shown = ref(false);
let observer = null;

const reduceMotion =
    typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

onMounted(() => {
    if (reduceMotion || typeof IntersectionObserver === 'undefined') {
        shown.value = true;
        return;
    }

    observer = new IntersectionObserver(
        entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    shown.value = true;
                    if (props.once && observer) {
                        observer.disconnect();
                        observer = null;
                    }
                } else if (!props.once) {
                    shown.value = false;
                }
            });
        },
        { threshold: props.threshold, rootMargin: '0px 0px -8% 0px' }
    );

    if (root.value) observer.observe(root.value);
});

onBeforeUnmount(() => {
    observer?.disconnect();
    observer = null;
});
</script>

<template>
    <component
        :is="as"
        ref="root"
        class="reveal"
        :class="{ 'reveal--in': shown }"
        :style="{
            '--reveal-y': y + 'px',
            '--reveal-delay': delay + 'ms',
            '--reveal-duration': duration + 'ms'
        }"
    >
        <slot />
    </component>
</template>
