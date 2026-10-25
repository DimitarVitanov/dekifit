<template>
    <AppLayout title="Checkout" description="Subscribe to a DekiFit package via bank transfer and get your personalized bodybuilding program and nutrition plan.">
        <section class="relative pt-32 pb-16 px-6 overflow-hidden">
            <div class="absolute inset-0">
                <Lightfall
                    :colors="['#f2b90c', '#e8b52c', '#f5f5f5']"
                    background-color="#050505"
                    :speed="0.3"
                    :streak-count="2"
                    :glow="1"
                    :opacity="0.4"
                    class="w-full h-full"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-dark/80 to-dark"></div>
            </div>

            <div class="relative z-10 max-w-3xl mx-auto text-center">
                <p class="text-emerald-400 text-xs uppercase tracking-[0.5em] font-semibold mb-6">Subscribe</p>
                <h1 class="font-display text-4xl md:text-6xl text-off-white mb-6 uppercase">
                    Get Your <span class="text-gradient">Program</span>
                </h1>
                <p class="text-text-body text-lg max-w-xl mx-auto">
                    Pick your package, fill in your details, and complete the bank transfer. Once payment is confirmed you receive a login to your client dashboard.
                </p>
            </div>
        </section>

        <section class="py-16 px-6">
            <div class="max-w-2xl mx-auto">
                <form @submit.prevent="submit" class="space-y-10">
                    <!-- Package Selection -->
                    <div class="bg-dark-grey/30 border border-emerald-500/10 rounded-2xl p-8">
                        <h2 class="font-display text-2xl text-off-white mb-6 uppercase">Choose Package</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <label
                                v-for="pkg in packagesList"
                                :key="pkg.slug"
                                class="relative flex flex-col gap-1 p-4 bg-dark-grey/50 border rounded-xl cursor-pointer transition-all duration-300"
                                :class="form.package_name === pkg.title ? 'border-emerald-500 bg-emerald-500/10' : 'border-emerald-500/20 hover:border-emerald-500/40'"
                            >
                                <input type="radio" v-model="selectedSlug" :value="pkg.slug" class="sr-only" @change="selectPackage(pkg)">
                                <span class="text-off-white font-semibold uppercase text-sm">{{ pkg.title }}</span>
                                <span class="text-emerald-400 font-display text-xl">€{{ pkg.price }}</span>
                            </label>
                        </div>
                        <p v-if="errors.package_name" class="text-red-400 text-sm mt-3">{{ errors.package_name }}</p>
                    </div>

                    <!-- Client Details -->
                    <div class="bg-dark-grey/30 border border-emerald-500/10 rounded-2xl p-8">
                        <h2 class="font-display text-2xl text-off-white mb-6 uppercase">Your Details</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm text-warm-grey mb-2">Full Name *</label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="w-full px-4 py-3 bg-dark-grey/50 border border-emerald-500/20 rounded-xl text-off-white placeholder-warm-grey focus:border-emerald-500/50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                    placeholder="Your name"
                                >
                            </div>
                            <div>
                                <label for="email" class="block text-sm text-warm-grey mb-2">Email *</label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="w-full px-4 py-3 bg-dark-grey/50 border border-emerald-500/20 rounded-xl text-off-white placeholder-warm-grey focus:border-emerald-500/50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                    placeholder="your@email.com"
                                >
                            </div>
                            <div class="sm:col-span-2">
                                <label for="phone" class="block text-sm text-warm-grey mb-2">Phone / WhatsApp / Viber *</label>
                                <input
                                    id="phone"
                                    v-model="form.phone"
                                    type="text"
                                    required
                                    class="w-full px-4 py-3 bg-dark-grey/50 border border-emerald-500/20 rounded-xl text-off-white placeholder-warm-grey focus:border-emerald-500/50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                    placeholder="+389 71 271 390"
                                >
                            </div>
                            <div class="sm:col-span-2">
                                <label for="notes" class="block text-sm text-warm-grey mb-2">Goals, experience level, dietary notes (optional)</label>
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="4"
                                    class="w-full px-4 py-3 bg-dark-grey/50 border border-emerald-500/20 rounded-xl text-off-white placeholder-warm-grey focus:border-emerald-500/50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all resize-none"
                                    placeholder="Tell me about your goals, training experience, or any dietary restrictions..."
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Bank Transfer Info -->
                    <div class="bg-dark-grey/30 border border-emerald-500/10 rounded-2xl p-8">
                        <h2 class="font-display text-2xl text-off-white mb-4 uppercase">Payment — Bank Transfer</h2>
                        <p class="text-text-body text-sm leading-relaxed mb-4">
                            After you submit this form, transfer <span class="text-off-white font-semibold">€{{ selectedPrice }}</span> in euros (EUR) to the account below. Once the transfer is confirmed, your client dashboard login is sent to your email.
                        </p>
                        <BankDetails :amount="selectedPrice" :reference="reference" />
                        <p class="text-warm-grey text-xs mt-4">All prices are in euros (EUR).</p>
                    </div>

                    <!-- Submit -->
                    <div class="text-center">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-16 py-5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-black text-sm tracking-[0.3em] uppercase font-bold rounded-xl hover:from-emerald-400 hover:to-emerald-500 transition-all duration-300 glow-emerald hover:glow-emerald-strong disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ form.processing ? 'Submitting...' : 'Submit Subscription' }}
                        </button>
                        <p class="text-warm-grey text-sm mt-4">I'll confirm your order within 24 hours.</p>
                    </div>
                </form>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Lightfall from '@/components/vue-bits/Lightfall.vue';
import BankDetails from '@/components/BankDetails.vue';
import { fallbackPackages, toPackage } from '@/data/packages';

const props = defineProps({
    packages: { type: Array, default: () => [] },
    selectedSlug: { type: String, default: null },
});

const packagesList = props.packages && props.packages.length
    ? props.packages.map(toPackage)
    : fallbackPackages;

const selectedSlug = ref(props.selectedSlug || packagesList[0]?.slug);

const initial = packagesList.find(p => p.slug === selectedSlug.value) || packagesList[0];

const form = useForm({
    program_id: initial?.id ?? null,
    package_name: initial?.title ?? '',
    price: initial?.price ?? '',
    name: '',
    email: '',
    phone: '',
    notes: '',
});

const errors = computed(() => form.errors);

const selectedPrice = computed(() => form.price);
const reference = computed(() => `DekiFit ${form.package_name} – ${form.name.trim() || 'your full name'}`);

function selectPackage(pkg) {
    form.program_id = pkg.id ?? null;
    form.package_name = pkg.title;
    form.price = pkg.price;
}

function submit() {
    form.post('/checkout', {
        preserveScroll: true,
    });
}
</script>
