<script setup>
import { computed, nextTick, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import PageHeader from '@/components/portal/PageHeader.vue';
import EmptyState from '@/components/portal/EmptyState.vue';
import VideoPlayer from '@/components/portal/VideoPlayer.vue';
import Icon from '@/components/portal/Icon.vue';

const props = defineProps({ videos: { type: Array, default: () => [] } });

const editing = ref(null);
const form = useForm({ title: '', category: '', url: '', description: '' });
const editor = ref(null);

const categories = computed(() => [...new Set(props.videos.map(video => video.category))]);

function edit(video) {
    editing.value = video.id;
    form.clearErrors();
    Object.assign(form, { title: video.title, category: video.category, url: video.url, description: video.description ?? '' });
    nextTick(() => editor.value?.scrollIntoView({ behavior: 'smooth', block: 'nearest' }));
}

function reset() {
    const category = form.category; // keep the category when adding several in a row
    editing.value = null;
    form.reset();
    form.clearErrors();
    form.category = category;
}

function save() {
    const options = { preserveScroll: true, onSuccess: reset };
    if (editing.value) form.put(`/admin/videos/${editing.value}`, options);
    else form.post('/admin/videos', options);
}

function remove(video) {
    if (confirm(`Remove "${video.title}" from the library?`)) {
        router.delete(`/admin/videos/${video.id}`, { preserveScroll: true, onSuccess: () => editing.value === video.id && reset() });
    }
}
</script>

<template>
    <PortalLayout title="Exercise videos">
        <PageHeader eyebrow="Coach admin" title="Exercise video library" subtitle="Add a link for each exercise. Every client sees the library, and you can attach videos to exercises in a training program." />

        <div class="grid grid-cols-1 xl:grid-cols-5 gap-4 sm:gap-6">
            <form ref="editor" class="p-card p-6 sm:p-8 xl:col-span-2 self-start" @submit.prevent="save">
                <h2 class="font-display text-xl uppercase text-off-white mb-5">{{ editing ? 'Edit video' : 'Add video' }}</h2>
                <label for="title" class="p-label">Exercise name</label>
                <input id="title" v-model="form.title" type="text" required maxlength="255" class="p-input" placeholder="e.g. Barbell bench press">
                <p v-if="form.errors.title" class="p-error">{{ form.errors.title }}</p>

                <label for="category" class="p-label mt-4">Category</label>
                <input id="category" v-model="form.category" type="text" required maxlength="60" list="video-categories" class="p-input" placeholder="e.g. Chest, Back, Legs">
                <datalist id="video-categories"><option v-for="item in categories" :key="item" :value="item" /></datalist>
                <p v-if="form.errors.category" class="p-error">{{ form.errors.category }}</p>

                <label for="url" class="p-label mt-4">Video link</label>
                <input id="url" v-model="form.url" type="text" inputmode="url" required maxlength="500" class="p-input" placeholder="https://youtube.com/watch?v=…">
                <p class="text-xs text-warm-grey mt-2">YouTube, Vimeo or a direct .mp4 link (or a /videos/… file on this site). Unlisted YouTube videos work too.</p>
                <p v-if="form.errors.url" class="p-error">{{ form.errors.url }}</p>

                <label for="description" class="p-label mt-4">Tips (optional)</label>
                <textarea id="description" v-model="form.description" rows="3" maxlength="2000" class="p-input resize-none" placeholder="Key cues for this exercise…"></textarea>

                <div class="flex flex-col-reverse sm:flex-row gap-3 mt-6">
                    <button v-if="editing" type="button" class="p-btn-ghost py-3" @click="reset">Cancel</button>
                    <button type="submit" class="p-btn flex-1" :disabled="form.processing">{{ form.processing ? 'Saving…' : editing ? 'Save changes' : 'Add to library' }}</button>
                </div>
            </form>

            <div class="xl:col-span-3 min-w-0">
                <div v-if="videos.length" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <article v-for="video in videos" :key="video.id" class="p-card p-3 sm:p-4">
                        <VideoPlayer :url="video.url" :title="video.title" :poster="video.poster" />
                        <div class="flex items-start justify-between gap-3 px-1 pt-3">
                            <div class="min-w-0">
                                <p class="p-eyebrow text-gold-400 mb-1">{{ video.category }}</p>
                                <h3 class="text-off-white font-semibold leading-snug break-words">{{ video.title }}</h3>
                            </div>
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <button type="button" class="p-2 text-warm-grey hover:text-gold-300 transition-colors" :aria-label="`Edit ${video.title}`" @click="edit(video)"><Icon name="note" class="w-4 h-4" /></button>
                                <button type="button" class="p-2 text-warm-grey hover:text-red-400 transition-colors" :aria-label="`Remove ${video.title}`" @click="remove(video)"><Icon name="trash" class="w-4 h-4" /></button>
                            </div>
                        </div>
                    </article>
                </div>
                <div v-else class="p-card">
                    <EmptyState icon="video" title="No videos yet" text="Add your first exercise video and it appears in every client's library." />
                </div>
            </div>
        </div>
    </PortalLayout>
</template>
