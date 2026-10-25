<template>
    <Head :title="title">
        <meta v-if="description" name="description" :content="description">
        <meta property="og:title" :content="title">
        <meta v-if="description" property="og:description" :content="description">
        <meta property="og:type" content="website">
        <meta property="og:image" content="/images/dekifit-logo-512.png">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="theme-color" content="#050505">
        <link rel="icon" type="image/png" href="/favicon-32.png">
    </Head>
    <div class="min-h-screen bg-dark">
        <SiteHeader />
        <main class="page-enter">
            <slot />
        </main>
        <SiteFooter />
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import SiteHeader from '@/components/SiteHeader.vue';
import SiteFooter from '@/components/SiteFooter.vue';

defineProps({
    title: { type: String, default: 'DekiFit — Train. Focus. Transform.' },
    description: { type: String, default: 'Personalized bodybuilding programs and nutrition plans by coach Dejan Badzukov. Build muscle, lose fat, and transform your physique with proven training methods.' },
});
</script>
