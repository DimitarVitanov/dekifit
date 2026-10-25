<script setup>
defineProps({
    eyebrow: { type: String, default: '' },
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
});
</script>

<template>
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6 lg:mb-8">
        <div class="min-w-0">
            <p v-if="eyebrow" class="p-eyebrow text-gold-400 mb-2">{{ eyebrow }}</p>
            <h1 class="font-display text-3xl sm:text-4xl uppercase text-off-white leading-tight break-words">{{ title }}</h1>
            <p v-if="subtitle" class="text-text-body mt-2 max-w-2xl">{{ subtitle }}</p>
        </div>
        <div v-if="$slots.default" class="flex-shrink-0">
            <slot />
        </div>
    </div>
</template>
