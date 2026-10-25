<script setup>
import { computed, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import PageHeader from '@/components/portal/PageHeader.vue';
import EmptyState from '@/components/portal/EmptyState.vue';
import StatusBadge from '@/components/portal/StatusBadge.vue';
import Icon from '@/components/portal/Icon.vue';
import { fmtDateTime } from '@/lib/format';

const props = defineProps({ orders: { type: Array, default: () => [] } });

const credentials = computed(() => usePage().props.flash?.credentials);
const busy = ref(null);
const copied = ref(false);

const pending = computed(() => props.orders.filter(order => order.status === 'pending'));
const others = computed(() => props.orders.filter(order => order.status !== 'pending'));

function grant(order) {
    if (!confirm(`Confirm that ${order.name} paid €${Number(order.price)} for ${order.package_name}?\n\nThis creates their dashboard login and emails it to ${order.email}.`)) return;
    busy.value = order.id;
    copied.value = false;
    router.post(`/admin/orders/${order.id}/grant`, {}, { preserveScroll: true, onFinish: () => { busy.value = null; } });
}

function cancel(order) {
    if (confirm(`Cancel the order from ${order.name}?`)) {
        router.post(`/admin/orders/${order.id}/cancel`, {}, { preserveScroll: true });
    }
}

async function copy() {
    const c = credentials.value;
    await navigator.clipboard.writeText(`DekiFit dashboard: ${window.location.origin}/login\nUsername: ${c.email}\nPassword: ${c.password}`);
    copied.value = true;
}
</script>

<template>
    <PortalLayout title="Orders">
        <PageHeader eyebrow="Coach admin" title="Orders" subtitle="When a bank transfer arrives, confirm it here — the client gets their dashboard login by email." />

        <section v-if="credentials" class="rounded-2xl border border-green-500/30 bg-green-500/10 p-5 sm:p-6 mb-6" role="status">
            <p class="text-green-200 font-semibold flex items-center gap-2"><Icon name="check" class="w-5 h-5" /> Access granted to {{ credentials.name }}</p>
            <template v-if="credentials.password">
                <p class="text-sm text-text-body mt-2">
                    {{ credentials.mailed ? 'The login was emailed to the client.' : 'The email could not be sent — pass these details to the client yourself.' }}
                    This password is shown only once.
                </p>
                <dl class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div class="rounded-xl bg-dark/50 border border-white/10 px-4 py-3"><dt class="text-warm-grey text-xs">Username</dt><dd class="text-off-white font-mono break-all">{{ credentials.email }}</dd></div>
                    <div class="rounded-xl bg-dark/50 border border-white/10 px-4 py-3"><dt class="text-warm-grey text-xs">Password</dt><dd class="text-off-white font-mono break-all">{{ credentials.password }}</dd></div>
                </dl>
                <button type="button" class="p-btn-ghost mt-4" @click="copy">{{ copied ? 'Copied' : 'Copy login details' }}</button>
            </template>
            <p v-else class="text-sm text-text-body mt-2">
                This client already had an account, so their package was activated on it and they keep their current password.
                {{ credentials.mailed ? 'They were notified by email.' : 'The notification email could not be sent.' }}
            </p>
        </section>

        <h2 class="font-display text-xl uppercase text-off-white mb-4">Awaiting payment <span class="text-warm-grey">({{ pending.length }})</span></h2>
        <div v-if="pending.length" class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-10">
            <article v-for="order in pending" :key="order.id" class="p-card p-5 sm:p-6 flex flex-col">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-off-white font-semibold text-lg truncate">{{ order.name }}</p>
                        <p class="text-xs text-warm-grey">{{ fmtDateTime(order.created_at) }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="font-display text-2xl text-gold-400 tabular-nums">€{{ Number(order.price) }}</p>
                        <p class="text-xs text-text-body uppercase tracking-wider">{{ order.package_name }}</p>
                    </div>
                </div>
                <dl class="mt-4 space-y-1 text-sm">
                    <div class="flex gap-2"><dt class="text-warm-grey w-14 flex-shrink-0">Email</dt><dd class="text-off-white break-all">{{ order.email }}</dd></div>
                    <div class="flex gap-2"><dt class="text-warm-grey w-14 flex-shrink-0">Phone</dt><dd class="text-off-white break-all">{{ order.phone }}</dd></div>
                    <div v-if="order.notes" class="flex gap-2"><dt class="text-warm-grey w-14 flex-shrink-0">Notes</dt><dd class="text-text-body break-words min-w-0">{{ order.notes }}</dd></div>
                </dl>
                <div class="flex flex-col sm:flex-row gap-3 mt-auto pt-5">
                    <button type="button" class="p-btn flex-1" :disabled="busy === order.id" @click="grant(order)">
                        <Icon name="check" class="w-4 h-4" />
                        {{ busy === order.id ? 'Granting…' : 'Confirm payment & grant access' }}
                    </button>
                    <button type="button" class="p-btn-ghost py-3" @click="cancel(order)">Cancel</button>
                </div>
            </article>
        </div>
        <div v-else class="p-card mb-10">
            <EmptyState icon="orders" title="No orders waiting" text="New subscription requests from the website appear here." />
        </div>

        <template v-if="others.length">
            <h2 class="font-display text-xl uppercase text-off-white mb-4">History</h2>
            <div class="p-card overflow-x-auto">
                <table class="w-full text-sm min-w-[640px]">
                    <thead>
                        <tr class="text-left text-warm-grey border-b border-white/5">
                            <th class="font-medium py-3 px-4 sm:px-6">Client</th>
                            <th class="font-medium py-3 px-3">Package</th>
                            <th class="font-medium py-3 px-3 text-right">Price</th>
                            <th class="font-medium py-3 px-3">Date</th>
                            <th class="font-medium py-3 px-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="order in others" :key="order.id" class="border-b border-white/5 last:border-0">
                            <td class="py-3 px-4 sm:px-6"><p class="text-off-white">{{ order.name }}</p><p class="text-xs text-warm-grey">{{ order.email }}</p></td>
                            <td class="py-3 px-3 text-text-body">{{ order.package_name }}</td>
                            <td class="py-3 px-3 text-right tabular-nums text-off-white">€{{ Number(order.price) }}</td>
                            <td class="py-3 px-3 text-text-body whitespace-nowrap">{{ fmtDateTime(order.created_at) }}</td>
                            <td class="py-3 px-3"><StatusBadge :status="order.status" viewer="admin" /></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>
    </PortalLayout>
</template>
