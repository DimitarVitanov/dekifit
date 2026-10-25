<script setup>
import { computed, onBeforeUnmount, ref } from 'vue';
import { bank, groupIban } from '@/data/bank';

// Bank transfer details with one-tap copy, shown at checkout and again after the order is placed.
const props = defineProps({
    amount: { type: [Number, String], default: null },
    reference: { type: String, default: '' }, // payment description, so the coach can match the transfer
});

const euros = computed(() => (props.amount === null || props.amount === '' ? null : Number(props.amount)));

const rows = computed(() => [
    euros.value !== null && { label: 'Amount', value: `€${euros.value} EUR`, copy: String(euros.value), strong: true },
    { label: 'Beneficiary', value: bank.beneficiary, copy: bank.beneficiary },
    { label: 'IBAN', value: groupIban(bank.iban), copy: bank.iban, mono: true },
    { label: 'BIC / SWIFT', value: bank.bic, copy: bank.bic, mono: true },
    // Non-breaking hyphen on screen so "5/4-7" never splits across lines; the copy keeps a plain one.
    { label: 'Beneficiary address', value: bank.address.replace(/-/g, '\u2011'), copy: bank.address },
    { label: 'Bank', value: `${bank.bankName}, ${bank.bankAddress}` },
    props.reference && { label: 'Payment description', value: props.reference, copy: props.reference },
].filter(Boolean));

const canCopy = typeof navigator !== 'undefined' && !!navigator.clipboard;
const copied = ref(null);
let timer = null;

async function copy(row) {
    try {
        await navigator.clipboard.writeText(row.copy);
        copied.value = row.label;
        clearTimeout(timer);
        timer = setTimeout(() => { copied.value = null; }, 1600);
    } catch {
        // Clipboard blocked: the value is still on screen to select by hand.
    }
}

onBeforeUnmount(() => clearTimeout(timer));
</script>

<template>
    <div class="bg-dark-warm/60 border border-emerald-500/30 rounded-xl text-sm text-left">
        <dl class="divide-y divide-emerald-500/10">
            <div v-for="row in rows" :key="row.label" class="flex items-center gap-3 px-4 sm:px-5 py-3.5">
                <div class="min-w-0 flex-1">
                    <dt class="text-warm-grey text-xs uppercase tracking-wider mb-0.5">{{ row.label }}</dt>
                    <dd
                        class="break-words"
                        :class="[row.strong ? 'text-emerald-400 font-display text-xl' : 'text-off-white', row.mono && 'font-mono text-[13px] sm:text-sm sm:tracking-wide']"
                    >{{ row.value }}</dd>
                </div>
                <button
                    v-if="canCopy && row.copy"
                    type="button"
                    class="shrink-0 flex items-center justify-center gap-1.5 w-9 h-9 sm:w-auto sm:h-auto sm:min-w-[4.5rem] sm:px-3 sm:py-1.5 rounded-lg border text-xs font-semibold uppercase tracking-wider transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400"
                    :class="copied === row.label ? 'border-emerald-500 text-emerald-400' : 'border-emerald-500/20 text-warm-grey hover:border-emerald-500/50 hover:text-off-white'"
                    :aria-label="`Copy ${row.label.toLowerCase()}`"
                    @click="copy(row)"
                >
                    <svg v-if="copied === row.label" class="w-4 h-4 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <svg v-else class="w-4 h-4 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" /></svg>
                    <span class="hidden sm:inline">{{ copied === row.label ? 'Copied' : 'Copy' }}</span>
                </button>
            </div>
        </dl>
        <details class="group border-t border-emerald-500/10 px-4 sm:px-5 py-3.5">
            <summary class="cursor-pointer list-none flex items-center justify-between text-warm-grey text-xs uppercase tracking-wider hover:text-off-white transition-colors">
                Correspondent banks (EUR), only if your bank asks
                <svg class="w-4 h-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
            </summary>
            <ul class="mt-3 space-y-1.5 text-warm-grey">
                <li v-for="c in bank.correspondentsEur" :key="c.bic"><span class="font-mono text-off-white">{{ c.bic }}</span> · {{ c.name }}</li>
            </ul>
        </details>
    </div>
</template>
