<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import PageHeader from '@/components/portal/PageHeader.vue';
import EmptyState from '@/components/portal/EmptyState.vue';
import StatusBadge from '@/components/portal/StatusBadge.vue';
import Icon from '@/components/portal/Icon.vue';
import { fromNow } from '@/lib/format';

defineProps({ tickets: { type: Array, default: () => [] } });

const composing = ref(false);
const form = useForm({ subject: '', body: '' });

const submit = () => form.post('/portal/support');
</script>

<template>
    <PortalLayout title="Ask your coach">
        <PageHeader eyebrow="Support" title="Ask your coach" subtitle="Questions about training, nutrition or your plan — send them here and Dejan will reply.">
            <button v-if="!composing" type="button" class="p-btn w-full sm:w-auto" @click="composing = true">
                <Icon name="plus" class="w-4 h-4" /> New question
            </button>
        </PageHeader>

        <form v-if="composing" class="p-card border-gold-500/30 p-6 sm:p-8 mb-6 max-w-3xl" @submit.prevent="submit">
            <h2 class="font-display text-xl uppercase text-off-white mb-5">New question</h2>
            <label for="subject" class="p-label">Subject</label>
            <input id="subject" v-model="form.subject" type="text" required maxlength="255" class="p-input" placeholder="e.g. Shoulder pain on bench press">
            <p v-if="form.errors.subject" class="p-error">{{ form.errors.subject }}</p>
            <label for="body" class="p-label mt-4">Your question</label>
            <textarea id="body" v-model="form.body" rows="5" required maxlength="5000" class="p-input" placeholder="Give as much detail as you can…"></textarea>
            <p v-if="form.errors.body" class="p-error">{{ form.errors.body }}</p>
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 mt-6">
                <button type="button" class="p-btn-ghost py-3" @click="composing = false">Cancel</button>
                <button type="submit" class="p-btn" :disabled="form.processing">{{ form.processing ? 'Sending…' : 'Send to coach' }}</button>
            </div>
        </form>

        <div class="p-card max-w-3xl overflow-hidden">
            <ul v-if="tickets.length" class="divide-y divide-white/5">
                <li v-for="ticket in tickets" :key="ticket.id">
                    <Link :href="`/portal/support/${ticket.id}`" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-4 px-4 sm:px-6 py-4 hover:bg-white/5 transition-colors">
                        <div class="min-w-0">
                            <p class="text-off-white font-medium truncate">{{ ticket.subject }}</p>
                            <p class="text-xs text-warm-grey mt-0.5">{{ ticket.messages_count }} {{ ticket.messages_count === 1 ? 'message' : 'messages' }} · updated {{ fromNow(ticket.last_reply_at) }}</p>
                        </div>
                        <StatusBadge :status="ticket.status" class="self-start sm:self-auto" />
                    </Link>
                </li>
            </ul>
            <EmptyState v-else icon="chat" title="No questions yet" text="Whenever something is unclear, ask — that's what your coach is here for." />
        </div>
    </PortalLayout>
</template>
