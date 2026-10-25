<script setup>
import { Link } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import PageHeader from '@/components/portal/PageHeader.vue';
import EmptyState from '@/components/portal/EmptyState.vue';
import Icon from '@/components/portal/Icon.vue';
import { fmtDate, initials } from '@/lib/format';

defineProps({ clients: { type: Array, default: () => [] } });
</script>

<template>
    <PortalLayout title="Clients">
        <PageHeader eyebrow="Coach admin" title="Clients" :subtitle="`${clients.length} ${clients.length === 1 ? 'client has' : 'clients have'} dashboard access.`" />

        <div v-if="clients.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
            <Link v-for="client in clients" :key="client.id" :href="`/admin/clients/${client.id}`" class="p-card p-5 sm:p-6 hover:border-gold-500/30 transition-colors group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-white/10 text-off-white text-sm font-bold flex items-center justify-center flex-shrink-0">{{ initials(client.name) }}</div>
                    <div class="min-w-0 flex-1">
                        <p class="text-off-white font-semibold truncate group-hover:text-gold-300 transition-colors">{{ client.name }}</p>
                        <p class="text-xs text-warm-grey truncate">{{ client.email }}</p>
                    </div>
                    <Icon name="arrow" class="w-4 h-4 text-warm-grey flex-shrink-0" />
                </div>
                <dl class="grid grid-cols-3 gap-3 mt-5 pt-4 border-t border-white/5 text-sm">
                    <div><dt class="text-xs text-warm-grey">Package</dt><dd class="text-off-white mt-0.5">{{ client.program?.title ?? '—' }}</dd></div>
                    <div><dt class="text-xs text-warm-grey">Check-ins</dt><dd class="text-off-white mt-0.5 tabular-nums">{{ client.progress_entries_count }}</dd></div>
                    <div><dt class="text-xs text-warm-grey">Last log</dt><dd class="text-off-white mt-0.5">{{ client.progress_entries_max_logged_on ? fmtDate(client.progress_entries_max_logged_on, { day: 'numeric', month: 'short' }) : '—' }}</dd></div>
                </dl>
            </Link>
        </div>
        <div v-else class="p-card">
            <EmptyState icon="users" title="No clients yet" text="Confirm a paid order and the client appears here with their own dashboard." />
        </div>
    </PortalLayout>
</template>
