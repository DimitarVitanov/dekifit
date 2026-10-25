<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

// Lazy background video: nothing is downloaded until the card scrolls near the
// viewport. The poster image stays visible (with a small loader) until the video
// can actually play, then the video fades in over it. Playback pauses off-screen.
const props = defineProps({
    src: { type: String, required: true },
    poster: { type: String, required: true },
    alt: { type: String, default: '' },
    posterClass: { type: String, default: 'w-3/4 h-3/4 object-contain' },
    rootMargin: { type: String, default: '200px' }
});

const root = ref(null);
const video = ref(null);
const activeSrc = ref(null);
const loading = ref(false);
const ready = ref(false);
let observer = null;

const reduceMotion =
    typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function onCanPlay() {
    ready.value = true;
    loading.value = false;
}

function onError() {
    // Keep the poster image if the video can't be loaded.
    loading.value = false;
}

function play() {
    video.value?.play()?.catch(() => {});
}

onMounted(() => {
    if (reduceMotion || typeof IntersectionObserver === 'undefined') return;

    observer = new IntersectionObserver(
        entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    if (!activeSrc.value) {
                        activeSrc.value = props.src;
                        loading.value = true;
                    } else {
                        play();
                    }
                } else if (activeSrc.value) {
                    video.value?.pause();
                }
            });
        },
        { rootMargin: props.rootMargin }
    );
    observer.observe(root.value);
});

onBeforeUnmount(() => {
    observer?.disconnect();
});
</script>

<template>
    <div ref="root" class="relative w-full h-full flex items-center justify-center">
        <img
            :src="poster"
            :alt="alt"
            width="600"
            height="600"
            loading="lazy"
            :class="[posterClass, 'transition-opacity duration-700', ready ? 'opacity-0' : 'opacity-100']"
        >
        <video
            v-if="activeSrc"
            ref="video"
            :src="activeSrc"
            autoplay
            muted
            loop
            playsinline
            preload="auto"
            aria-hidden="true"
            class="absolute inset-0 w-full h-full object-cover transition-opacity duration-700"
            :class="ready ? 'opacity-100' : 'opacity-0'"
            @canplay="onCanPlay"
            @error="onError"
        />
        <div
            v-if="loading"
            class="absolute bottom-4 left-4 w-6 h-6 rounded-full border-2 border-emerald-400/30 border-t-emerald-400 animate-spin"
            role="status"
            aria-label="Loading video"
        />
    </div>
</template>
