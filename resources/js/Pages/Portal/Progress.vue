<script setup>
import { computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import PageHeader from '@/components/portal/PageHeader.vue';
import WeightChart from '@/components/portal/WeightChart.vue';
import ProgressTable from '@/components/portal/ProgressTable.vue';
import EmptyState from '@/components/portal/EmptyState.vue';
import { fmtDate, localDateKey } from '@/lib/format';

const props = defineProps({ entries: { type: Array, default: () => [] } });

const form = useForm({ logged_on: localDateKey(), weight_kg: '', body_fat_pct: '', waist_cm: '', note: '' });

const series = computed(() => props.entries.map(e => ({ date: e.logged_on, value: e.weight_kg })));
const existing = computed(() => props.entries.find(e => e.logged_on === form.logged_on));

function submit() {
    form.post('/portal/progress', {
        preserveScroll: true,
        onSuccess: () => form.reset('weight_kg', 'body_fat_pct', 'waist_cm', 'note'),
    });
}

function remove(entry) {
    if (confirm(`Delete the entry from ${fmtDate(entry.logged_on)}?`)) {
        router.delete(`/portal/progress/${entry.id}`, { preserveScroll: true });
    }
}
</script>

<template>
    <PortalLayout title="Progress">
        <PageHeader eyebrow="Tracking" title="Your progress" subtitle="Log your check-ins and watch the trend. Your coach sees these too." />

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6">
            <form class="p-card p-6 sm:p-8 xl:order-2 self-start" @submit.prevent="submit">
                <h2 class="font-display text-xl uppercase text-off-white mb-5">Log a check-in</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label for="logged_on" class="p-label">Date</label>
                        <input id="logged_on" v-model="form.logged_on" type="date" required :max="localDateKey()" class="p-input">
                        <p v-if="form.errors.logged_on" class="p-error">{{ form.errors.logged_on }}</p>
                    </div>
                    <div class="col-span-2">
                        <label for="weight_kg" class="p-label">Weight (kg) *</label>
                        <input id="weight_kg" v-model="form.weight_kg" type="number" inputmode="decimal" step="0.1" min="25" max="400" required class="p-input" placeholder="82.4">
                        <p v-if="form.errors.weight_kg" class="p-error">{{ form.errors.weight_kg }}</p>
                    </div>
                    <div>
                        <label for="body_fat_pct" class="p-label">Body fat (%)</label>
                        <input id="body_fat_pct" v-model="form.body_fat_pct" type="number" inputmode="decimal" step="0.1" min="2" max="70" class="p-input" placeholder="Optional">
                        <p v-if="form.errors.body_fat_pct" class="p-error">{{ form.errors.body_fat_pct }}</p>
                    </div>
                    <div>
                        <label for="waist_cm" class="p-label">Waist (cm)</label>
                        <input id="waist_cm" v-model="form.waist_cm" type="number" inputmode="decimal" step="0.1" min="30" max="250" class="p-input" placeholder="Optional">
                        <p v-if="form.errors.waist_cm" class="p-error">{{ form.errors.waist_cm }}</p>
                    </div>
                    <div class="col-span-2">
                        <label for="note" class="p-label">Note</label>
                        <textarea id="note" v-model="form.note" rows="3" maxlength="500" class="p-input resize-none" placeholder="How did the week go? Energy, sleep, training…"></textarea>
                        <p v-if="form.errors.note" class="p-error">{{ form.errors.note }}</p>
                    </div>
                </div>
                <p v-if="existing" class="text-xs text-gold-300 mt-4">You already logged {{ existing.weight_kg }} kg on this date — saving will replace it.</p>
                <button type="submit" class="p-btn w-full mt-6" :disabled="form.processing">{{ form.processing ? 'Saving…' : 'Save check-in' }}</button>
            </form>

            <div class="xl:col-span-2 xl:order-1 space-y-4 sm:space-y-6 min-w-0">
                <section class="p-card p-4 sm:p-8">
                    <h2 class="font-display text-xl uppercase text-off-white mb-4 px-2 sm:px-0">Weight trend <span class="text-warm-grey text-sm normal-case font-sans font-normal">(kg)</span></h2>
                    <WeightChart :points="series" :height="280" />
                </section>

                <section class="p-card overflow-hidden">
                    <h2 class="font-display text-xl uppercase text-off-white px-4 sm:px-6 pt-6 pb-2">History</h2>
                    <ProgressTable v-if="entries.length" :entries="entries" removable @remove="remove" />
                    <EmptyState v-else icon="trend" title="No check-ins yet" text="Log your first weigh-in to start your progress history." />
                </section>
            </div>
        </div>
    </PortalLayout>
</template>
