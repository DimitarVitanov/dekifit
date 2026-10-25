<script setup>
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import PageHeader from '@/components/portal/PageHeader.vue';
import EmptyState from '@/components/portal/EmptyState.vue';
import StatusBadge from '@/components/portal/StatusBadge.vue';
import WeightChart from '@/components/portal/WeightChart.vue';
import ProgressTable from '@/components/portal/ProgressTable.vue';
import Icon from '@/components/portal/Icon.vue';
import { fmtDate, fmtDateTime, fromNow } from '@/lib/format';

const props = defineProps({
    client: { type: Object, required: true },
    entries: { type: Array, default: () => [] },
    sessions: { type: Array, default: () => [] },
    tickets: { type: Array, default: () => [] },
    libraryCategories: { type: Object, default: () => ({}) }, // { Chest: 9, … }
    unlockedCategories: { type: Array, default: () => [] },
});

const series = computed(() => props.entries.map(e => ({ date: e.logged_on, value: e.weight_kg })));

const library = useForm({ categories: [...props.unlockedCategories] });
const allCategories = computed(() => Object.keys(props.libraryCategories));
const isUnlocked = category => library.categories.includes(category);

function toggle(category) {
    library.categories = isUnlocked(category)
        ? library.categories.filter(item => item !== category)
        : [...library.categories, category];
}

function saveLibrary() {
    library.put(`/admin/clients/${props.client.id}/library`, {
        preserveScroll: true,
        onSuccess: () => library.defaults({ categories: [...library.categories] }),
    });
}
</script>

<template>
    <PortalLayout :title="client.name">
        <Link href="/admin/clients" class="inline-flex items-center gap-2 text-sm text-warm-grey hover:text-gold-300 mb-4"><Icon name="back" class="w-4 h-4" /> All clients</Link>
        <PageHeader :eyebrow="client.program ? `${client.program} package` : 'Client'" :title="client.name" :subtitle="`${client.email}${client.phone ? ' · ' + client.phone : ''} · client since ${fmtDate(client.created_at)}`" />

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6">
            <div class="xl:col-span-2 space-y-4 sm:space-y-6 min-w-0">
                <section class="p-card p-4 sm:p-8">
                    <h2 class="font-display text-xl uppercase text-off-white mb-4 px-2 sm:px-0">Weight trend <span class="text-warm-grey text-sm normal-case font-sans font-normal">(kg)</span></h2>
                    <WeightChart :points="series" :height="260" />
                </section>
                <section class="p-card overflow-hidden">
                    <h2 class="font-display text-xl uppercase text-off-white px-4 sm:px-6 pt-6 pb-2">Check-ins</h2>
                    <ProgressTable v-if="entries.length" :entries="entries" />
                    <EmptyState v-else icon="trend" title="No check-ins yet" />
                </section>
            </div>

            <div class="space-y-4 sm:space-y-6">
                <form class="p-card p-6" @submit.prevent="saveLibrary">
                    <h2 class="font-display text-xl uppercase text-off-white">Video library</h2>
                    <p class="text-sm text-warm-grey mt-1 mb-4">Unlock the body parts this client can watch. Videos in their training program always play.</p>
                    <template v-if="allCategories.length">
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="category in allCategories"
                                :key="category"
                                type="button"
                                :aria-pressed="isUnlocked(category)"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-full border text-sm font-semibold transition-colors"
                                :class="isUnlocked(category) ? 'bg-gold-500 border-gold-500 text-black' : 'border-white/10 text-warm-grey hover:border-gold-500/40 hover:text-off-white'"
                                @click="toggle(category)"
                            >
                                <Icon :name="isUnlocked(category) ? 'check' : 'lock'" class="w-3.5 h-3.5" />
                                {{ category }} <span class="opacity-60 tabular-nums">{{ libraryCategories[category] }}</span>
                            </button>
                        </div>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-4 text-sm">
                            <button type="button" class="text-gold-300 hover:text-gold-200 font-semibold" @click="library.categories = [...allCategories]">Unlock all</button>
                            <button type="button" class="text-warm-grey hover:text-off-white font-semibold" @click="library.categories = []">Lock all</button>
                        </div>
                        <p v-if="library.errors.categories" class="p-error">{{ library.errors.categories }}</p>
                        <button type="submit" class="p-btn w-full mt-5" :disabled="!library.isDirty || library.processing">{{ library.processing ? 'Saving…' : 'Save access' }}</button>
                    </template>
                    <p v-else class="text-sm text-warm-grey">The video library is empty.</p>
                </form>
                <section class="p-card p-6">
                    <h2 class="font-display text-xl uppercase text-off-white mb-4">Sessions</h2>
                    <ul v-if="sessions.length" class="space-y-3 text-sm">
                        <li v-for="slot in sessions" :key="slot.id" class="border-l-2 border-gold-500/40 pl-3">
                            <p class="text-off-white">{{ fmtDateTime(slot.starts_at) }}</p>
                            <p v-if="slot.client_note" class="text-text-body break-words">“{{ slot.client_note }}”</p>
                        </li>
                    </ul>
                    <p v-else class="text-sm text-warm-grey">No sessions booked.</p>
                </section>
                <section class="p-card p-6">
                    <h2 class="font-display text-xl uppercase text-off-white mb-4">Tickets</h2>
                    <ul v-if="tickets.length" class="divide-y divide-white/5">
                        <li v-for="ticket in tickets" :key="ticket.id">
                            <Link :href="`/admin/tickets/${ticket.id}`" class="block py-3 group">
                                <p class="text-off-white text-sm font-medium truncate group-hover:text-gold-300">{{ ticket.subject }}</p>
                                <div class="flex items-center justify-between gap-3 mt-1.5">
                                    <StatusBadge :status="ticket.status" viewer="admin" />
                                    <span class="text-xs text-warm-grey">{{ fromNow(ticket.last_reply_at) }}</span>
                                </div>
                            </Link>
                        </li>
                    </ul>
                    <p v-else class="text-sm text-warm-grey">No questions asked.</p>
                </section>
            </div>
        </div>
    </PortalLayout>
</template>
