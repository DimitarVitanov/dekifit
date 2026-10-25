<script setup>
import { computed, ref, watch } from 'vue';
import Icon from './Icon.vue';
import { fmtDate, localDateKey } from '@/lib/format';
import { currentWeek, dayLabel, daysByWeek, planDate, todayIndex } from '@/lib/plan';

// Calendar for a plan split into weeks: one week of real dates at a time, or the
// whole program at a glance. v-model is the selected position in plan.days.
const props = defineProps({
    plan: { type: Object, required: true },
    memberSince: { type: String, default: null },
    modelValue: { type: Number, default: 0 },
});
const emit = defineEmits(['update:modelValue']);

const today = localDateKey();
const thisWeek = computed(() => currentWeek(props.plan, props.memberSince));

const weeks = computed(() => Object.entries(daysByWeek(props.plan)).map(([week, days]) => {
    week = Number(week);
    const cells = Array.from({ length: Math.max(7, days.length) }, (_, position) => {
        const date = planDate(props.plan, props.memberSince, (week - 1) * 7 + position);
        const entry = days[position];
        return {
            position,
            date,
            index: entry ? entry.index : null,
            label: entry ? dayLabel(entry.day.name) : '',
            name: entry?.day.name ?? '',
            rest: !entry?.day.exercises.length,
            today: localDateKey(date) === today,
        };
    });
    return { week, cells };
}));

const lastWeek = computed(() => weeks.value[weeks.value.length - 1].week);
const selectedWeek = computed(() => props.plan.days[props.modelValue]?.week ?? thisWeek.value);
const shownWeek = ref(selectedWeek.value);
watch(selectedWeek, week => { shownWeek.value = week; });
const shown = computed(() => weeks.value.find(row => row.week === shownWeek.value) ?? weeks.value[0]);

const short = date => fmtDate(date, { day: 'numeric', month: 'short' });
const range = computed(() => `${short(shown.value.cells[0].date)} – ${short(shown.value.cells[shown.value.cells.length - 1].date)}`);

const expanded = ref(false);

function pick(cell) {
    if (cell.index === null) return;
    emit('update:modelValue', cell.index);
    expanded.value = false;
}

// Step a week, keeping the same day of the week selected where there is one.
function step(by) {
    const target = weeks.value.find(row => row.week === shown.value.week + by);
    if (!target) return;
    const position = shown.value.cells.findIndex(cell => cell.index === props.modelValue);
    const cell = target.cells[position]?.index != null ? target.cells[position] : target.cells.find(item => item.index !== null);
    emit('update:modelValue', cell.index);
}

const todaysDay = computed(() => todayIndex(props.plan, props.memberSince));
const goToday = () => emit('update:modelValue', todaysDay.value);

const cellClass = cell => {
    if (cell.index === null) return 'border-transparent text-warm-grey/40 cursor-default';
    if (cell.index === props.modelValue) return 'bg-gold-500 border-gold-500 text-black';
    if (cell.today) return 'border-gold-500/60 text-off-white hover:border-gold-400';
    return cell.rest ? 'border-white/5 text-warm-grey hover:border-gold-500/30' : 'border-white/10 bg-white/[0.03] text-off-white hover:border-gold-500/40';
};
const selected = cell => cell.index !== null && cell.index === props.modelValue;
const cellLabel = cell => (cell.index === null ? '' : `${fmtDate(cell.date, { weekday: 'long', day: 'numeric', month: 'long' })}: ${cell.name}${cell.today ? ' (today)' : ''}`);
</script>

<template>
    <section class="p-card p-4 sm:p-6">
        <div class="flex items-center gap-2 mb-4">
            <div class="min-w-0 flex-1">
                <h2 class="font-display text-xl sm:text-2xl uppercase text-off-white leading-tight">
                    Week {{ shown.week }} <span class="text-warm-grey">/ {{ lastWeek }}</span>
                </h2>
                <p class="text-xs sm:text-sm text-warm-grey mt-0.5">{{ range }}<span v-if="shown.week === thisWeek" class="text-gold-300"> · this week</span></p>
            </div>
            <button v-if="modelValue !== todaysDay" type="button" class="px-3 py-2 text-sm font-semibold text-gold-300 hover:text-gold-200" @click="goToday">Today</button>
            <button type="button" class="w-10 h-10 rounded-xl border border-white/10 text-off-white flex items-center justify-center hover:border-gold-500/40 disabled:opacity-30 disabled:hover:border-white/10" :disabled="shown.week === weeks[0].week" aria-label="Previous week" @click="step(-1)">
                <Icon name="back" class="w-4 h-4" />
            </button>
            <button type="button" class="w-10 h-10 rounded-xl border border-white/10 text-off-white flex items-center justify-center hover:border-gold-500/40 disabled:opacity-30 disabled:hover:border-white/10" :disabled="shown.week === lastWeek" aria-label="Next week" @click="step(1)">
                <Icon name="arrow" class="w-4 h-4" />
            </button>
        </div>

        <!-- One week -->
        <div v-if="!expanded" class="grid grid-cols-7 gap-1.5 sm:gap-2">
            <button
                v-for="cell in shown.cells"
                :key="cell.position"
                type="button"
                :disabled="cell.index === null"
                :aria-pressed="selected(cell)"
                :aria-label="cellLabel(cell)"
                class="relative min-w-0 rounded-xl border px-1 py-2 sm:py-3 text-center transition-colors"
                :class="cellClass(cell)"
                @click="pick(cell)"
            >
                <span v-if="cell.today" class="absolute top-1.5 right-1.5 w-1.5 h-1.5 rounded-full" :class="selected(cell) ? 'bg-black' : 'bg-gold-400'"></span>
                <span class="block text-[10px] sm:text-[11px] uppercase tracking-wider" :class="selected(cell) ? 'text-black/70' : 'text-warm-grey'">{{ fmtDate(cell.date, { weekday: 'short' }) }}</span>
                <span class="block font-display text-xl sm:text-2xl leading-tight tabular-nums">{{ fmtDate(cell.date, { day: 'numeric' }) }}</span>
                <span class="block text-[10px] sm:text-xs font-semibold truncate" :class="selected(cell) ? 'text-black/80' : cell.rest ? 'text-warm-grey' : 'text-gold-300'">{{ cell.label || '·' }}</span>
            </button>
        </div>

        <!-- Whole program -->
        <div v-else class="space-y-1.5">
            <div v-for="row in weeks" :key="row.week" class="grid grid-cols-[2.75rem_repeat(7,minmax(0,1fr))] gap-1 sm:gap-1.5 items-center">
                <span class="leading-tight" :class="row.week === thisWeek ? 'text-gold-300' : 'text-warm-grey'">
                    <span class="block text-xs font-semibold tabular-nums">W{{ row.week }}</span>
                    <span class="block text-[10px] whitespace-nowrap">{{ short(row.cells[0].date) }}</span>
                </span>
                <button
                    v-for="cell in row.cells.slice(0, 7)"
                    :key="cell.position"
                    type="button"
                    :disabled="cell.index === null"
                    :aria-pressed="selected(cell)"
                    :aria-label="cellLabel(cell)"
                    class="relative h-10 rounded-lg border text-sm tabular-nums transition-colors"
                    :class="cellClass(cell)"
                    @click="pick(cell)"
                >
                    {{ fmtDate(cell.date, { day: 'numeric' }) }}
                    <span v-if="cell.index !== null && !cell.rest" class="absolute bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full" :class="selected(cell) ? 'bg-black' : 'bg-gold-400'"></span>
                </button>
            </div>
            <p class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-warm-grey pt-2">
                <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-gold-400"></span> Training day</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded border border-gold-500/60"></span> Today</span>
            </p>
        </div>

        <button type="button" class="mt-4 w-full flex items-center justify-center gap-2 py-2 rounded-xl text-sm font-semibold text-warm-grey hover:text-off-white hover:bg-white/[0.03] transition-colors" :aria-expanded="expanded" @click="expanded = !expanded">
            <Icon name="calendar" class="w-4 h-4" /> {{ expanded ? 'Show one week' : `Full ${lastWeek}-week calendar` }}
        </button>
    </section>
</template>
