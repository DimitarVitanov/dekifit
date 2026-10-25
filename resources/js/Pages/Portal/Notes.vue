<script setup>
import { nextTick, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import PageHeader from '@/components/portal/PageHeader.vue';
import EmptyState from '@/components/portal/EmptyState.vue';
import Icon from '@/components/portal/Icon.vue';
import { fromNow } from '@/lib/format';

defineProps({ notes: { type: Array, default: () => [] } });

const editing = ref(null); // null = closed, 'new' or a note id
const form = useForm({ title: '', body: '' });
const editor = ref(null);

function open(note = null) {
    editing.value = note ? note.id : 'new';
    form.clearErrors();
    form.title = note?.title ?? '';
    form.body = note?.body ?? '';
    nextTick(() => editor.value?.scrollIntoView({ behavior: 'smooth', block: 'nearest' }));
}

function close() {
    editing.value = null;
    form.reset();
}

function save() {
    const options = { preserveScroll: true, onSuccess: close };
    if (editing.value === 'new') form.post('/portal/notes', options);
    else form.put(`/portal/notes/${editing.value}`, options);
}

function remove(note) {
    if (confirm(`Delete "${note.title}"?`)) {
        router.delete(`/portal/notes/${note.id}`, { preserveScroll: true, onSuccess: () => editing.value === note.id && close() });
    }
}
</script>

<template>
    <PortalLayout title="Notes">
        <PageHeader eyebrow="Private" title="Your notes" subtitle="Training cues, personal records, reminders — only you can see these.">
            <button type="button" class="p-btn w-full sm:w-auto" @click="open()">
                <Icon name="plus" class="w-4 h-4" /> New note
            </button>
        </PageHeader>

        <form v-if="editing" ref="editor" class="p-card border-gold-500/30 p-6 sm:p-8 mb-6" @submit.prevent="save">
            <h2 class="font-display text-xl uppercase text-off-white mb-5">{{ editing === 'new' ? 'New note' : 'Edit note' }}</h2>
            <label for="note-title" class="p-label">Title</label>
            <input id="note-title" v-model="form.title" type="text" required maxlength="255" class="p-input" placeholder="e.g. Squat cues">
            <p v-if="form.errors.title" class="p-error">{{ form.errors.title }}</p>
            <label for="note-body" class="p-label mt-4">Note</label>
            <textarea id="note-body" v-model="form.body" rows="6" maxlength="10000" class="p-input" placeholder="Write anything you want to remember…"></textarea>
            <p v-if="form.errors.body" class="p-error">{{ form.errors.body }}</p>
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 mt-6">
                <button type="button" class="p-btn-ghost py-3" @click="close">Cancel</button>
                <button type="submit" class="p-btn" :disabled="form.processing">{{ form.processing ? 'Saving…' : 'Save note' }}</button>
            </div>
        </form>

        <div v-if="notes.length" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
            <article v-for="note in notes" :key="note.id" class="p-card p-6 flex flex-col">
                <h2 class="text-off-white font-semibold text-lg leading-snug break-words">{{ note.title }}</h2>
                <p class="text-text-body text-sm mt-2 whitespace-pre-wrap break-words line-clamp-6 flex-1">{{ note.body }}</p>
                <div class="flex items-center justify-between gap-3 mt-5 pt-4 border-t border-white/5">
                    <p class="text-xs text-warm-grey">Edited {{ fromNow(note.updated_at) }}</p>
                    <div class="flex items-center gap-1">
                        <button type="button" class="p-2 text-warm-grey hover:text-gold-300 transition-colors" :aria-label="`Edit ${note.title}`" @click="open(note)"><Icon name="note" class="w-4 h-4" /></button>
                        <button type="button" class="p-2 text-warm-grey hover:text-red-400 transition-colors" :aria-label="`Delete ${note.title}`" @click="remove(note)"><Icon name="trash" class="w-4 h-4" /></button>
                    </div>
                </div>
            </article>
        </div>
        <div v-else-if="!editing" class="p-card">
            <EmptyState icon="note" title="No notes yet" text="Write down what you want to remember from your training and nutrition.">
                <button type="button" class="p-btn-ghost" @click="open()">Write your first note</button>
            </EmptyState>
        </div>
    </PortalLayout>
</template>
