<script setup>
import { computed, ref, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import PageHeader from '@/components/portal/PageHeader.vue';
import EmptyState from '@/components/portal/EmptyState.vue';
import Icon from '@/components/portal/Icon.vue';
import { fmtDate, fmtDay, fmtTime, fromNow, localDateKey, timezoneName } from '@/lib/format';

const props = defineProps({
    available: { type: Array, default: () => [] },
    booked: { type: Array, default: () => [] },
});

// Group free slots by the viewer's local calendar day.
const days = computed(() => {
    const map = new Map();
    props.available.forEach(slot => {
        const key = localDateKey(slot.starts_at);
        if (!map.has(key)) map.set(key, []);
        map.get(key).push(slot);
    });
    return [...map.entries()].map(([key, slots]) => ({ key, slots }));
});

const activeDay = ref(days.value[0]?.key ?? null);
const selected = ref(null);
const form = useForm({ client_note: '' });

watch(days, list => {
    if (!list.some(day => day.key === activeDay.value)) activeDay.value = list[0]?.key ?? null;
    if (selected.value && !props.available.some(slot => slot.id === selected.value.id)) selected.value = null;
});

const daySlots = computed(() => days.value.find(day => day.key === activeDay.value)?.slots ?? []);

function pickDay(key) {
    activeDay.value = key;
    selected.value = null;
}

function book() {
    form.post(`/portal/schedule/${selected.value.id}/book`, {
        preserveScroll: true,
        onSuccess: () => {
            selected.value = null;
            form.reset();
        },
    });
}

function cancel(slot) {
    if (confirm(`Cancel your session on ${fmtDay(slot.starts_at)} at ${fmtTime(slot.starts_at)}?`)) {
        router.post(`/portal/schedule/${slot.id}/cancel`, {}, { preserveScroll: true });
    }
}
</script>

<template>
    <PortalLayout title="Schedule">
        <PageHeader eyebrow="Sessions" title="Schedule a meeting" :subtitle="`Pick a free time with your coach. Times are shown in your timezone (${timezoneName()}).`" />

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6">
            <section class="p-card xl:col-span-2 p-4 sm:p-8 min-w-0">
                <h2 class="font-display text-xl uppercase text-off-white mb-5">Available times</h2>

                <template v-if="days.length">
                    <div class="flex gap-2 overflow-x-auto no-scrollbar pb-3 -mx-4 px-4 sm:mx-0 sm:px-0" role="tablist" aria-label="Day">
                        <button
                            v-for="day in days"
                            :key="day.key"
                            type="button"
                            role="tab"
                            :aria-selected="day.key === activeDay"
                            class="flex-shrink-0 w-[4.5rem] py-3 rounded-2xl border text-center transition-colors"
                            :class="day.key === activeDay ? 'bg-gold-500 border-gold-500 text-black' : 'border-white/10 text-off-white hover:border-gold-500/40'"
                            @click="pickDay(day.key)"
                        >
                            <span class="block text-[11px] uppercase tracking-wider" :class="day.key === activeDay ? 'text-black/70' : 'text-warm-grey'">{{ fmtDate(day.key, { weekday: 'short' }) }}</span>
                            <span class="block font-display text-2xl leading-tight">{{ fmtDate(day.key, { day: 'numeric' }) }}</span>
                            <span class="block text-[11px]" :class="day.key === activeDay ? 'text-black/70' : 'text-warm-grey'">{{ fmtDate(day.key, { month: 'short' }) }}</span>
                        </button>
                    </div>

                    <p class="text-sm text-text-body mt-4 mb-3">{{ fmtDay(activeDay) }}</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        <button
                            v-for="slot in daySlots"
                            :key="slot.id"
                            type="button"
                            :aria-pressed="selected?.id === slot.id"
                            class="py-3 rounded-xl border text-sm font-semibold tabular-nums transition-colors"
                            :class="selected?.id === slot.id ? 'bg-gold-500/15 border-gold-500 text-gold-200' : 'border-white/10 text-off-white hover:border-gold-500/40'"
                            @click="selected = slot"
                        >
                            {{ fmtTime(slot.starts_at) }}
                        </button>
                    </div>

                    <form v-if="selected" class="mt-6 p-4 sm:p-6 rounded-2xl border border-gold-500/30 bg-gold-500/5" @submit.prevent="book">
                        <p class="text-off-white font-semibold">{{ fmtDay(selected.starts_at) }}</p>
                        <p class="text-gold-300 tabular-nums">{{ fmtTime(selected.starts_at) }} – {{ fmtTime(selected.ends_at) }}</p>
                        <label for="client_note" class="p-label mt-4">What would you like to talk about? (optional)</label>
                        <textarea id="client_note" v-model="form.client_note" rows="2" maxlength="500" class="p-input resize-none"></textarea>
                        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 mt-4">
                            <button type="button" class="p-btn-ghost py-3" @click="selected = null">Back</button>
                            <button type="submit" class="p-btn" :disabled="form.processing">{{ form.processing ? 'Booking…' : 'Confirm booking' }}</button>
                        </div>
                    </form>
                </template>
                <EmptyState v-else icon="calendar" title="No free times right now" text="Your coach hasn't opened new slots yet. Check back soon or send a message." />
            </section>

            <section class="p-card p-6 sm:p-8 self-start">
                <h2 class="font-display text-xl uppercase text-off-white mb-5">Your sessions</h2>
                <ul v-if="booked.length" class="space-y-4">
                    <li v-for="slot in booked" :key="slot.id" class="rounded-2xl border border-white/10 p-4">
                        <p class="text-off-white font-semibold">{{ fmtDay(slot.starts_at) }}</p>
                        <p class="text-gold-300 tabular-nums">{{ fmtTime(slot.starts_at) }} – {{ fmtTime(slot.ends_at) }}</p>
                        <p class="text-xs text-warm-grey mt-1">{{ fromNow(slot.starts_at) }}</p>
                        <p v-if="slot.client_note" class="text-sm text-text-body mt-3 break-words">“{{ slot.client_note }}”</p>
                        <div class="flex flex-wrap gap-3 mt-4">
                            <a v-if="slot.meeting_url" :href="slot.meeting_url" target="_blank" rel="noopener" class="p-btn py-2.5"><Icon name="video" class="w-4 h-4" /> Join call</a>
                            <button type="button" class="p-btn-ghost" @click="cancel(slot)">Cancel</button>
                        </div>
                    </li>
                </ul>
                <EmptyState v-else icon="video" title="Nothing booked" text="Choose a time and it will show up here." />
            </section>
        </div>
    </PortalLayout>
</template>
