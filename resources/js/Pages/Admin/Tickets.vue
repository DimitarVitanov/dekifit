<script setup>
import { Link } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import PageHeader from '@/components/portal/PageHeader.vue';
import EmptyState from '@/components/portal/EmptyState.vue';
import StatusBadge from '@/components/portal/StatusBadge.vue';
import { fromNow } from '@/lib/format';

defineProps({ tickets: { type: Array, default: () => [] } });
</script>

<template>
    <PortalLayout title="Tickets">
        <PageHeader eyebrow="Coach admin" title="Client questions" subtitle="Questions that need your reply are listed first." />

        <div class="p-card overflow-hidden max-w-4xl">
            <ul v-if="tickets.length" class="divide-y divide-white/5">
                <li v-for="ticket in tickets" :key="ticket.id">
                    <Link :href="`/admin/tickets/${ticket.id}`" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-4 px-4 sm:px-6 py-4 hover:bg-white/5 transition-colors">
                        <div class="min-w-0">
                            <p class="text-off-white font-medium truncate">{{ ticket.subject }}</p>
                            <p class="text-xs text-warm-grey mt-0.5">{{ ticket.user?.name }} · {{ fromNow(ticket.last_reply_at) }}</p>
                        </div>
                        <StatusBadge :status="ticket.status" viewer="admin" class="self-start sm:self-auto" />
                    </Link>
                </li>
            </ul>
            <EmptyState v-else icon="chat" title="No questions yet" text="Client questions from the dashboard land here." />
        </div>
    </PortalLayout>
</template>
