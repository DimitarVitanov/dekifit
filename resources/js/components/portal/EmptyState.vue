<script setup>
import Icon from './Icon.vue';

defineProps({
    icon: { type: String, default: 'note' },
    title: { type: String, required: true },
    text: { type: String, default: '' },
});
</script>

<template>
    <div class="flex flex-col items-center text-center py-10 px-6">
        <div class="w-12 h-12 rounded-2xl bg-gold-500/10 text-gold-400 flex items-center justify-center mb-4">
            <Icon :name="icon" class="w-6 h-6" />
        </div>
        <p class="text-off-white font-semibold">{{ title }}</p>
        <p v-if="text" class="text-warm-grey text-sm mt-1 max-w-sm">{{ text }}</p>
        <div v-if="$slots.default" class="mt-5"><slot /></div>
    </div>
</template>
