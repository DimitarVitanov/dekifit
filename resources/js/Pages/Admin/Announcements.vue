<script setup>
import { nextTick, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import PageHeader from '@/components/portal/PageHeader.vue';
import EmptyState from '@/components/portal/EmptyState.vue';
import Icon from '@/components/portal/Icon.vue';
import { fmtDateTime } from '@/lib/format';

defineProps({ announcements: { type: Array, default: () => [] } });

const editing = ref(null); // null = new post, otherwise the announcement id
const form = useForm({ title: '', body: '', is_pinned: false });
const editor = ref(null);

function edit(item) {
    editing.value = item.id;
    form.clearErrors();
    form.title = item.title;
    form.body = item.body;
    form.is_pinned = item.is_pinned;
    nextTick(() => editor.value?.scrollIntoView({ behavior: 'smooth', block: 'nearest' }));
}

function reset() {
    editing.value = null;
    form.reset();
    form.clearErrors();
}

function save() {
    const options = { preserveScroll: true, onSuccess: reset };
    if (editing.value) form.put(`/admin/news/${editing.value}`, options);
    else form.post('/admin/news', options);
}

function remove(item) {
    if (confirm(`Delete "${item.title}"?`)) {
        router.delete(`/admin/news/${item.id}`, { preserveScroll: true, onSuccess: () => editing.value === item.id && reset() });
    }
}
</script>

<template>
    <PortalLayout title="News">
        <PageHeader eyebrow="Coach admin" title="News & announcements" subtitle="Everything you post here shows up on every client's dashboard straight away." />

        <div class="grid grid-cols-1 xl:grid-cols-5 gap-4 sm:gap-6">
            <form ref="editor" class="p-card p-6 sm:p-8 xl:col-span-2 self-start" @submit.prevent="save">
                <h2 class="font-display text-xl uppercase text-off-white mb-5">{{ editing ? 'Edit post' : 'New post' }}</h2>
                <label for="title" class="p-label">Title</label>
                <input id="title" v-model="form.title" type="text" required maxlength="255" class="p-input" placeholder="e.g. This week's focus: sleep">
                <p v-if="form.errors.title" class="p-error">{{ form.errors.title }}</p>
                <label for="body" class="p-label mt-4">Message</label>
                <textarea id="body" v-model="form.body" rows="7" required maxlength="10000" class="p-input"></textarea>
                <p v-if="form.errors.body" class="p-error">{{ form.errors.body }}</p>
                <label class="flex items-center gap-3 text-sm text-text-body cursor-pointer mt-4">
                    <input v-model="form.is_pinned" type="checkbox" class="w-4 h-4 accent-gold-500">
                    Pin to the top
                </label>
                <div class="flex flex-col-reverse sm:flex-row gap-3 mt-6">
                    <button v-if="editing" type="button" class="p-btn-ghost py-3" @click="reset">Cancel</button>
                    <button type="submit" class="p-btn flex-1" :disabled="form.processing">{{ form.processing ? 'Saving…' : editing ? 'Save changes' : 'Publish' }}</button>
                </div>
            </form>

            <div class="xl:col-span-3 space-y-4 min-w-0">
                <article v-for="item in announcements" :key="item.id" class="p-card p-5 sm:p-6" :class="item.is_pinned ? 'border-gold-500/30' : ''">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-xs text-warm-grey mb-1">
                                <span v-if="item.is_pinned" class="text-gold-300 font-semibold">Pinned · </span>{{ fmtDateTime(item.published_at) }}
                            </p>
                            <h3 class="text-off-white font-semibold text-lg leading-snug break-words">{{ item.title }}</h3>
                        </div>
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <button type="button" class="p-2 text-warm-grey hover:text-gold-300 transition-colors" :aria-label="`Edit ${item.title}`" @click="edit(item)"><Icon name="note" class="w-4 h-4" /></button>
                            <button type="button" class="p-2 text-warm-grey hover:text-red-400 transition-colors" :aria-label="`Delete ${item.title}`" @click="remove(item)"><Icon name="trash" class="w-4 h-4" /></button>
                        </div>
                    </div>
                    <p class="text-text-body text-sm mt-2 whitespace-pre-wrap break-words line-clamp-4">{{ item.body }}</p>
                </article>
                <div v-if="!announcements.length" class="p-card">
                    <EmptyState icon="bell" title="Nothing posted yet" text="Write your first update for your clients." />
                </div>
            </div>
        </div>
    </PortalLayout>
</template>
