<script setup>
import Icon from './Icon.vue';
import { fmtDate } from '@/lib/format';

defineProps({
    entries: { type: Array, required: true }, // newest first
    removable: { type: Boolean, default: false },
});

defineEmits(['remove']);

// Change vs. the previous (older) entry.
const delta = (entries, i) => {
    const prev = entries[i + 1];
    if (!prev) return null;
    const d = Number((entries[i].weight_kg - prev.weight_kg).toFixed(1));
    return d === 0 ? '±0' : d > 0 ? `+${d}` : `${d}`;
};
</script>

<template>
    <!-- Phones: one card per check-in, nothing to scroll sideways -->
    <ul class="md:hidden divide-y divide-white/5">
        <li v-for="(entry, i) in entries" :key="entry.id" class="px-4 py-4">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs text-warm-grey">{{ fmtDate(entry.logged_on) }}</p>
                    <p class="font-display text-2xl text-off-white tabular-nums leading-tight">
                        {{ entry.weight_kg }} kg
                        <span v-if="delta(entries, i)" class="font-sans text-sm font-medium text-text-body ml-1">{{ delta(entries, i) }} kg</span>
                    </p>
                </div>
                <button v-if="removable" type="button" class="p-2.5 -mr-2 text-warm-grey hover:text-red-400 transition-colors flex-shrink-0" :aria-label="`Delete entry from ${fmtDate(entry.logged_on)}`" @click="$emit('remove', entry)">
                    <Icon name="trash" class="w-4 h-4" />
                </button>
            </div>
            <div v-if="entry.body_fat_pct != null || entry.waist_cm != null" class="flex flex-wrap gap-2 mt-2.5">
                <span v-if="entry.body_fat_pct != null" class="px-2.5 py-1 rounded-full bg-white/5 text-xs text-text-body tabular-nums">Body fat {{ entry.body_fat_pct }}%</span>
                <span v-if="entry.waist_cm != null" class="px-2.5 py-1 rounded-full bg-white/5 text-xs text-text-body tabular-nums">Waist {{ entry.waist_cm }} cm</span>
            </div>
            <p v-if="entry.note" class="text-sm text-text-body mt-2.5 break-words">{{ entry.note }}</p>
        </li>
    </ul>

    <!-- Tablets and up: full table -->
    <div class="hidden md:block relative overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-warm-grey border-b border-white/5">
                    <th class="font-medium py-3 px-6">Date</th>
                    <th class="font-medium py-3 px-3 text-right">Weight</th>
                    <th class="font-medium py-3 px-3 text-right">Change</th>
                    <th class="font-medium py-3 px-3 text-right">Body fat</th>
                    <th class="font-medium py-3 px-3 text-right">Waist</th>
                    <th class="font-medium py-3 px-3">Note</th>
                    <th v-if="removable" class="py-3 px-3" aria-label="Actions"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(entry, i) in entries" :key="entry.id" class="border-b border-white/5 last:border-0">
                    <td class="py-3 px-6 whitespace-nowrap text-off-white">{{ fmtDate(entry.logged_on) }}</td>
                    <td class="py-3 px-3 text-right tabular-nums text-off-white font-semibold whitespace-nowrap">{{ entry.weight_kg }} kg</td>
                    <td class="py-3 px-3 text-right tabular-nums text-text-body">{{ delta(entries, i) ?? '—' }}</td>
                    <td class="py-3 px-3 text-right tabular-nums text-text-body">{{ entry.body_fat_pct != null ? `${entry.body_fat_pct}%` : '—' }}</td>
                    <td class="py-3 px-3 text-right tabular-nums text-text-body whitespace-nowrap">{{ entry.waist_cm != null ? `${entry.waist_cm} cm` : '—' }}</td>
                    <td class="py-3 px-3 text-text-body min-w-40">{{ entry.note || '—' }}</td>
                    <td v-if="removable" class="py-3 px-3 text-right">
                        <button type="button" class="p-2 -m-2 text-warm-grey hover:text-red-400 transition-colors" :aria-label="`Delete entry from ${fmtDate(entry.logged_on)}`" @click="$emit('remove', entry)">
                            <Icon name="trash" class="w-4 h-4" />
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
