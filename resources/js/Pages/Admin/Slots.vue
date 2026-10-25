<script setup>
import { computed } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import PageHeader from '@/components/portal/PageHeader.vue';
import EmptyState from '@/components/portal/EmptyState.vue';
import Icon from '@/components/portal/Icon.vue';
import { fmtDay, fmtTime, localDateKey, timezoneName } from '@/lib/format';

const props = defineProps({ slots: { type: Array, default: () => [] } });

const tomorrow = new Date();
tomorrow.setDate(tomorrow.getDate() + 1);

const form = useForm({
    date: localDateKey(tomorrow),
    from: '17:00',
    to: '20:00',
    length: 30,
    weeks: 1,
    meeting_url: '',
});

// Build the slots in the coach's local time; the browser converts each to UTC.
const generated = computed(() => {
    if (!form.date || !form.from || !form.to) return [];
    const list = [];
    for (let week = 0; week < form.weeks; week++) {
        const start = new Date(`${form.date}T${form.from}`);
        const end = new Date(`${form.date}T${form.to}`);
        start.setDate(start.getDate() + week * 7);
        end.setDate(end.getDate() + week * 7);
        for (let t = new Date(start); t.getTime() + form.length * 60000 <= end.getTime(); t = new Date(t.getTime() + form.length * 60000)) {
            list.push({ starts_at: t.toISOString(), ends_at: new Date(t.getTime() + form.length * 60000).toISOString() });
        }
    }
    return list;
});

const days = computed(() => {
    const map = new Map();
    props.slots.forEach(slot => {
        const key = localDateKey(slot.starts_at);
        if (!map.has(key)) map.set(key, []);
        map.get(key).push(slot);
    });
    return [...map.entries()].map(([key, slots]) => ({ key, slots }));
});

const bookedCount = computed(() => props.slots.filter(slot => slot.user_id).length);

function submit() {
    form.transform(data => ({ slots: generated.value, meeting_url: data.meeting_url || null }))
        .post('/admin/slots', { preserveScroll: true });
}

function remove(slot) {
    const message = slot.user
        ? `This slot is booked by ${slot.user.name}. Remove it anyway? Let the client know yourself.`
        : `Remove the ${fmtTime(slot.starts_at)} slot?`;
    if (confirm(message)) router.delete(`/admin/slots/${slot.id}`, { preserveScroll: true });
}
</script>

<template>
    <PortalLayout title="Time slots">
        <PageHeader eyebrow="Coach admin" title="Time slots" :subtitle="`Open the times you're available — clients book them from their dashboard. Times are in your timezone (${timezoneName()}).`" />

        <div class="grid grid-cols-1 xl:grid-cols-5 gap-4 sm:gap-6">
            <form class="p-card p-6 sm:p-8 xl:col-span-2 self-start" @submit.prevent="submit">
                <h2 class="font-display text-xl uppercase text-off-white mb-5">Add availability</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label for="date" class="p-label">Date</label>
                        <input id="date" v-model="form.date" type="date" required :min="localDateKey()" class="p-input">
                    </div>
                    <div>
                        <label for="from" class="p-label">From</label>
                        <input id="from" v-model="form.from" type="time" required class="p-input">
                    </div>
                    <div>
                        <label for="to" class="p-label">To</label>
                        <input id="to" v-model="form.to" type="time" required class="p-input">
                    </div>
                    <div>
                        <label for="length" class="p-label">Session length</label>
                        <select id="length" v-model.number="form.length" class="p-input">
                            <option :value="15">15 min</option>
                            <option :value="30">30 min</option>
                            <option :value="45">45 min</option>
                            <option :value="60">60 min</option>
                        </select>
                    </div>
                    <div>
                        <label for="weeks" class="p-label">Repeat weekly</label>
                        <select id="weeks" v-model.number="form.weeks" class="p-input">
                            <option :value="1">Just this day</option>
                            <option v-for="n in [2, 3, 4, 6, 8]" :key="n" :value="n">For {{ n }} weeks</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label for="meeting_url" class="p-label">Video call link (optional)</label>
                        <input id="meeting_url" v-model="form.meeting_url" type="url" class="p-input" placeholder="https://meet.google.com/…">
                        <p v-if="form.errors.meeting_url" class="p-error">{{ form.errors.meeting_url }}</p>
                    </div>
                </div>
                <p class="text-sm mt-4" :class="generated.length ? 'text-text-body' : 'text-red-400'">
                    {{ generated.length ? `This adds ${generated.length} slot${generated.length === 1 ? '' : 's'}. Times that overlap existing slots are skipped.` : 'The end time must be after the start time, with room for at least one session.' }}
                </p>
                <p v-if="form.errors.slots || Object.keys(form.errors).some(key => key.startsWith('slots.'))" class="p-error">Some of these times are in the past — pick a later time.</p>
                <button type="submit" class="p-btn w-full mt-6" :disabled="form.processing || !generated.length">
                    <Icon name="plus" class="w-4 h-4" /> {{ form.processing ? 'Adding…' : 'Add slots' }}
                </button>
            </form>

            <section class="xl:col-span-3 min-w-0">
                <p class="text-sm text-text-body mb-4">{{ slots.length }} upcoming · {{ bookedCount }} booked</p>
                <div v-if="days.length" class="space-y-4">
                    <div v-for="day in days" :key="day.key" class="p-card p-5 sm:p-6">
                        <h2 class="text-off-white font-semibold mb-4">{{ fmtDay(day.key) }}</h2>
                        <ul class="space-y-2">
                            <li v-for="slot in day.slots" :key="slot.id" class="flex items-center gap-3 rounded-xl border px-3 sm:px-4 py-2.5" :class="slot.user ? 'border-gold-500/30 bg-gold-500/5' : 'border-white/10'">
                                <span class="tabular-nums text-sm font-semibold text-off-white whitespace-nowrap">{{ fmtTime(slot.starts_at) }} – {{ fmtTime(slot.ends_at) }}</span>
                                <span class="flex-1 min-w-0 text-sm">
                                    <template v-if="slot.user">
                                        <Link :href="`/admin/clients/${slot.user.id}`" class="text-gold-300 font-semibold hover:text-gold-200">{{ slot.user.name }}</Link>
                                        <span v-if="slot.client_note" class="block text-text-body text-xs break-words">“{{ slot.client_note }}”</span>
                                    </template>
                                    <span v-else class="text-warm-grey">Free</span>
                                </span>
                                <button type="button" class="p-2 -m-1 text-warm-grey hover:text-red-400 transition-colors flex-shrink-0" :aria-label="`Remove ${fmtTime(slot.starts_at)} slot`" @click="remove(slot)"><Icon name="trash" class="w-4 h-4" /></button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div v-else class="p-card">
                    <EmptyState icon="calendar" title="No upcoming slots" text="Add your availability and clients can start booking sessions." />
                </div>
            </section>
        </div>
    </PortalLayout>
</template>
