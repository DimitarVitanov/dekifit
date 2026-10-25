<script setup>
import { Link, router } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import PageHeader from '@/components/portal/PageHeader.vue';
import StatusBadge from '@/components/portal/StatusBadge.vue';
import TicketThread from '@/components/portal/TicketThread.vue';
import Icon from '@/components/portal/Icon.vue';

const props = defineProps({ ticket: { type: Object, required: true }, messages: { type: Array, required: true } });

const close = () => router.post(`/admin/tickets/${props.ticket.id}/close`, {}, { preserveScroll: true });
</script>

<template>
    <PortalLayout :title="ticket.subject">
        <Link href="/admin/tickets" class="inline-flex items-center gap-2 text-sm text-warm-grey hover:text-gold-300 mb-4"><Icon name="back" class="w-4 h-4" /> All questions</Link>
        <div class="max-w-3xl">
            <PageHeader :title="ticket.subject">
                <div class="flex flex-wrap items-center gap-3">
                    <StatusBadge :status="ticket.status" viewer="admin" />
                    <button v-if="ticket.status !== 'closed'" type="button" class="p-btn-ghost" @click="close">Close ticket</button>
                </div>
            </PageHeader>
            <p class="text-sm text-text-body -mt-3 mb-6">
                From <Link :href="`/admin/clients/${ticket.client.id}`" class="text-gold-400 hover:text-gold-300 font-semibold">{{ ticket.client.name }}</Link>
                <span class="text-warm-grey"> · {{ ticket.client.email }}</span>
            </p>
            <TicketThread :messages="messages" :reply-url="`/admin/tickets/${ticket.id}`" :closed="ticket.status === 'closed'" />
        </div>
    </PortalLayout>
</template>
