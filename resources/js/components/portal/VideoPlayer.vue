<script setup>
import { computed, ref, watch } from 'vue';
import Icon from './Icon.vue';
import { parseVideo } from '@/lib/video';

// Click-to-load player: nothing heavy (iframe / video file) is fetched until the
// client presses play, so a library full of videos stays fast.
const props = defineProps({
    url: { type: String, required: true },
    title: { type: String, default: 'Video' },
    autoplay: { type: Boolean, default: false },
    poster: { type: String, default: null }, // preview image; YouTube links bring their own
});

const video = computed(() => parseVideo(props.url));
const playing = ref(props.autoplay);
const posterFailed = ref(false);
const poster = computed(() => (posterFailed.value ? null : props.poster || video.value.thumbnail));

watch(() => props.url, () => { playing.value = props.autoplay; posterFailed.value = false; });
</script>

<template>
    <div class="relative aspect-video rounded-xl overflow-hidden bg-black">
        <template v-if="video.type === 'link'">
            <a :href="video.url" target="_blank" rel="noopener" class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-sm text-gold-300 hover:text-gold-200">
                <Icon name="video" class="w-8 h-8" />
                Open video
            </a>
        </template>
        <template v-else-if="playing">
            <iframe v-if="video.type === 'embed'" :src="video.src" :title="title" class="absolute inset-0 w-full h-full" allow="autoplay; encrypted-media; picture-in-picture; fullscreen" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
            <video v-else :src="video.src" :poster="poster" class="absolute inset-0 w-full h-full object-contain" controls autoplay playsinline></video>
        </template>
        <button v-else type="button" class="group absolute inset-0 w-full h-full" :aria-label="`Play ${title}`" @click="playing = true">
            <img v-if="poster" :src="poster" alt="" loading="lazy" class="absolute inset-0 w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity" @error="posterFailed = true">
            <span v-else class="absolute inset-0 bg-gradient-to-br from-gold-900/60 via-dark-grey to-dark flex items-center justify-center">
                <img src="/images/dekifit-logo.webp" alt="" loading="lazy" class="w-1/3 opacity-25">
            </span>
            <span class="absolute inset-0 flex items-center justify-center">
                <span class="w-14 h-14 rounded-full bg-gold-500 text-black flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform">
                    <Icon name="play" class="w-6 h-6 ml-0.5" />
                </span>
            </span>
        </button>
    </div>
</template>
