<script setup>
import { Link } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import PageHeader from '@/components/portal/PageHeader.vue';
import StatusBadge from '@/components/portal/StatusBadge.vue';
import TicketThread from '@/components/portal/TicketThread.vue';
import Icon from '@/components/portal/Icon.vue';

defineProps({ ticket: { type: Object, required: true }, messages: { type: Array, required: true } });
</script>

<template>
    <PortalLayout :title="ticket.subject">
        <Link href="/portal/support" class="inline-flex items-center gap-2 text-sm text-warm-grey hover:text-gold-300 mb-4"><Icon name="back" class="w-4 h-4" /> All questions</Link>
        <div class="max-w-3xl">
            <PageHeader :title="ticket.subject"><StatusBadge :status="ticket.status" /></PageHeader>
            <TicketThread :messages="messages" :reply-url="`/portal/support/${ticket.id}`" :closed="ticket.status === 'closed'" />
        </div>
    </PortalLayout>
</template>
