<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';

// Animates a numeric value up from zero when it scrolls into view, preserving any
// non-numeric prefix/suffix (e.g. "10+", "100%"). Falls back to the final value
// instantly for reduced motion or missing IntersectionObserver.
const props = defineProps({
    value: { type: [String, Number], required: true },
    duration: { type: Number, default: 1600 }
});

const parsed = computed(() => {
    const str = String(props.value);
    const match = str.match(/^(\D*)([\d.,]+)(\D*)$/);
    if (!match) return { prefix: '', target: null, suffix: str, decimals: 0 };
    const numeric = match[2].replace(/,/g, '');
    const decimals = numeric.includes('.') ? numeric.split('.')[1].length : 0;
    return { prefix: match[1], target: parseFloat(numeric), suffix: match[3], decimals };
});

const display = ref(parsed.value.target === null ? String(props.value) : formatted(0));

function formatted(n) {
    const { prefix, suffix, decimals } = parsed.value;
    return `${prefix}${n.toFixed(decimals)}${suffix}`;
}

const root = ref(null);
let observer = null;
let raf = null;

const reduceMotion =
    typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function run() {
    const { target } = parsed.value;
    if (target === null) return;

    const start = performance.now();
    const tick = now => {
        const t = Math.min((now - start) / props.duration, 1);
        const eased = 1 - Math.pow(1 - t, 3); // easeOutCubic
        display.value = formatted(target * eased);
        if (t < 1) {
            raf = requestAnimationFrame(tick);
        } else {
            display.value = formatted(target);
        }
    };
    raf = requestAnimationFrame(tick);
}

onMounted(() => {
    if (parsed.value.target === null) {
        display.value = String(props.value);
        return;
    }
    if (reduceMotion || typeof IntersectionObserver === 'undefined') {
        display.value = formatted(parsed.value.target);
        return;
    }

    observer = new IntersectionObserver(
        entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    run();
                    observer?.disconnect();
                    observer = null;
                }
            });
        },
        { threshold: 0.4 }
    );
    if (root.value) observer.observe(root.value);
});

onBeforeUnmount(() => {
    observer?.disconnect();
    observer = null;
    if (raf) cancelAnimationFrame(raf);
});
</script>

<template>
    <span ref="root">{{ display }}</span>
</template>
