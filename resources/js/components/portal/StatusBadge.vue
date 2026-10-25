<script setup>
import { computed } from 'vue';

// Status is always shown as a dot + label, never by colour alone.
const props = defineProps({ status: { type: String, required: true }, viewer: { type: String, default: 'client' } });

const map = {
    open: { client: 'Waiting for coach', admin: 'Needs reply', tone: 'gold' },
    answered: { client: 'Coach replied', admin: 'Answered', tone: 'green' },
    closed: { client: 'Closed', admin: 'Closed', tone: 'grey' },
    pending: { client: 'Pending', admin: 'Awaiting payment', tone: 'gold' },
    paid: { client: 'Paid', admin: 'Paid · access granted', tone: 'green' },
    delivered: { client: 'Delivered', admin: 'Delivered', tone: 'green' },
    cancelled: { client: 'Cancelled', admin: 'Cancelled', tone: 'grey' },
};

const tones = {
    gold: 'bg-gold-500/10 text-gold-300 border-gold-500/20',
    green: 'bg-green-500/10 text-green-300 border-green-500/20',
    grey: 'bg-white/5 text-warm-grey border-white/10',
};

const entry = computed(() => map[props.status] ?? { client: props.status, admin: props.status, tone: 'grey' });
</script>

<template>
    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-xs font-medium whitespace-nowrap" :class="tones[entry.tone]">
        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
        {{ entry[viewer] }}
    </span>
</template>
