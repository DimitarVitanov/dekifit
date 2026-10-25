<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import PageHeader from '@/components/portal/PageHeader.vue';
import EmptyState from '@/components/portal/EmptyState.vue';
import VideoPlayer from '@/components/portal/VideoPlayer.vue';
import Icon from '@/components/portal/Icon.vue';

const props = defineProps({ videos: { type: Array, default: () => [] } });

const category = ref('All');
const search = ref('');

// Unlocked videos first; the server sends locked ones without a link.
const sorted = computed(() => [...props.videos].sort((a, b) => a.locked - b.locked));

const categories = computed(() => ['All', ...new Set(sorted.value.map(video => video.category))]);
// A body part counts as locked until the coach unlocks it, even if a few of its videos play through the client's program.
const lockedCategories = computed(() => new Set(props.videos.filter(video => video.locked).map(video => video.category)));

const filtered = computed(() => {
    const term = search.value.trim().toLowerCase();
    return sorted.value.filter(video =>
        (category.value === 'All' || video.category === category.value) &&
        (!term || video.title.toLowerCase().includes(term))
    );
});
</script>

<template>
    <PortalLayout title="Exercise videos">
        <PageHeader eyebrow="Library" title="Exercise videos" subtitle="How to perform every exercise in your program, with the right technique." />

        <template v-if="videos.length">
            <div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-6">
                <div class="flex flex-wrap gap-2 lg:flex-1" role="tablist" aria-label="Category">
                    <button
                        v-for="item in categories"
                        :key="item"
                        type="button"
                        role="tab"
                        :aria-selected="item === category"
                        class="px-4 py-2 rounded-full border text-sm font-semibold transition-colors"
                        :class="item === category ? 'bg-gold-500 border-gold-500 text-black' : 'border-white/10 text-off-white hover:border-gold-500/40'"
                        @click="category = item"
                    >
                        <Icon v-if="lockedCategories.has(item)" name="lock" class="inline w-3.5 h-3.5 -mt-0.5 mr-1 opacity-70" />{{ item }}
                    </button>
                </div>
                <label class="lg:w-72">
                    <span class="sr-only">Search exercises</span>
                    <input v-model="search" type="search" class="p-input py-2.5" placeholder="Search exercises…">
                </label>
            </div>

            <div v-if="lockedCategories.size" class="p-card border-gold-500/20 bg-gold-500/5 p-4 sm:p-5 mb-6 flex flex-col sm:flex-row sm:items-center gap-4">
                <span class="w-10 h-10 rounded-xl bg-gold-500/10 text-gold-400 flex items-center justify-center flex-shrink-0"><Icon name="lock" class="w-5 h-5" /></span>
                <p class="text-sm text-text-body flex-1">
                    <span class="text-off-white font-semibold">{{ lockedCategories.size === categories.length - 1 ? 'Your video library is locked.' : `${[...lockedCategories].join(', ')} ${lockedCategories.size === 1 ? 'is' : 'are'} locked.` }}</span>
                    Your coach unlocks body parts as part of your coaching.
                </p>
                <Link href="/portal/support" class="p-btn-ghost flex-shrink-0"><Icon name="chat" class="w-4 h-4" /> Ask your coach</Link>
            </div>

            <div v-if="filtered.length" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
                <article v-for="video in filtered" :key="video.id" class="p-card p-3 sm:p-4">
                    <div v-if="video.locked" class="relative aspect-video rounded-xl overflow-hidden bg-black">
                        <img v-if="video.poster" :src="video.poster" alt="" loading="lazy" class="absolute inset-0 w-full h-full object-cover opacity-40 grayscale">
                        <span class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-sm font-semibold text-off-white">
                            <span class="w-12 h-12 rounded-full bg-black/60 border border-white/15 flex items-center justify-center"><Icon name="lock" class="w-5 h-5" /></span>
                            Locked
                        </span>
                    </div>
                    <VideoPlayer v-else :url="video.url" :title="video.title" :poster="video.poster" />
                    <div class="px-1 pt-4 pb-1">
                        <p class="p-eyebrow text-gold-400 mb-1">{{ video.category }}</p>
                        <h2 class="text-off-white font-semibold leading-snug break-words">{{ video.title }}</h2>
                        <p v-if="video.description" class="text-text-body text-sm mt-1.5 whitespace-pre-wrap break-words">{{ video.description }}</p>
                    </div>
                </article>
            </div>
            <div v-else class="p-card">
                <EmptyState icon="video" title="No videos match" text="Try another category or search term." />
            </div>
        </template>
        <div v-else class="p-card">
            <EmptyState icon="video" title="The library is being prepared" text="Your coach is adding exercise videos — check back soon." />
        </div>
    </PortalLayout>
</template>
