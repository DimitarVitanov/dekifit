<script setup>
import PortalLayout from '@/Layouts/PortalLayout.vue';
import PageHeader from '@/components/portal/PageHeader.vue';
import EmptyState from '@/components/portal/EmptyState.vue';
import Icon from '@/components/portal/Icon.vue';
import { fmtDate, fromNow } from '@/lib/format';

defineProps({ announcements: { type: Array, default: () => [] } });
</script>

<template>
    <PortalLayout title="News">
        <PageHeader eyebrow="From your coach" title="News & announcements" subtitle="Daily updates, tips and announcements from Dejan." />

        <div v-if="announcements.length" class="space-y-4 sm:space-y-6 max-w-3xl">
            <article v-for="item in announcements" :key="item.id" class="p-card p-6 sm:p-8" :class="item.is_pinned ? 'border-gold-500/30' : ''">
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-warm-grey mb-3">
                    <span v-if="item.is_pinned" class="inline-flex items-center gap-1.5 text-gold-300 font-semibold"><Icon name="pin" class="w-3.5 h-3.5" /> Pinned</span>
                    <time :datetime="item.published_at" :title="fmtDate(item.published_at)">{{ fromNow(item.published_at) }}</time>
                </div>
                <h2 class="font-display text-2xl uppercase text-off-white leading-snug mb-3">{{ item.title }}</h2>
                <p class="text-text-body leading-relaxed whitespace-pre-wrap break-words">{{ item.body }}</p>
            </article>
        </div>
        <div v-else class="p-card max-w-3xl">
            <EmptyState icon="bell" title="No news yet" text="When your coach posts an update it will appear here." />
        </div>
    </PortalLayout>
</template>
