<template>
    <AppLayout title="Subscription Received" description="Your DekiFit subscription request has been received.">
        <section class="relative min-h-[80vh] flex items-center justify-center px-6 pt-24 overflow-hidden">
            <div class="absolute inset-0">
                <Lightfall
                    :colors="['#f2b90c', '#e8b52c', '#f5f5f5']"
                    background-color="#050505"
                    :speed="0.3"
                    :streak-count="2"
                    :glow="1.2"
                    :opacity="0.5"
                    class="w-full h-full"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-dark/70 to-dark"></div>
            </div>

            <div class="relative z-10 max-w-2xl mx-auto text-center">
                <div class="w-20 h-20 rounded-full bg-emerald-500/15 border border-emerald-500/40 flex items-center justify-center mx-auto mb-8">
                    <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p class="text-emerald-400 text-xs uppercase tracking-[0.5em] font-semibold mb-4">Request Received</p>
                <h1 class="font-display text-4xl md:text-5xl text-off-white mb-6 uppercase">Thank You, {{ order.name.split(' ')[0] }}!</h1>
                <p class="text-text-body text-lg leading-relaxed mb-8">
                    Your <span class="text-emerald-400 font-semibold">{{ order.package_name }}</span> package request (€{{ Number(order.price) }}) has been received.
                    Complete your bank transfer in euros (EUR) using the details below, and once it's confirmed, your client dashboard login will be sent to <span class="text-off-white">{{ order.email }}</span> or via WhatsApp/Viber.
                </p>
                <BankDetails :amount="order.price" :reference="`DekiFit ${order.package_name} – ${order.name}`" class="mb-10" />
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="https://wa.me/38971271390" target="_blank" rel="noopener" class="px-8 py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 text-black text-sm tracking-[0.2em] uppercase font-bold rounded-lg hover:from-emerald-400 hover:to-emerald-500 transition-all duration-300">
                        Message on WhatsApp
                    </a>
                    <Link href="/" class="px-8 py-4 border border-emerald-500/30 text-emerald-400 text-sm tracking-[0.2em] uppercase font-semibold rounded-lg hover:bg-emerald-500/10 transition-all duration-300">
                        Back to Home
                    </Link>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Lightfall from '@/components/vue-bits/Lightfall.vue';
import BankDetails from '@/components/BankDetails.vue';

defineProps({
    order: { type: Object, required: true },
});
</script>
