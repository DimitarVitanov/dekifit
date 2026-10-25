<template>
    <AppLayout title="Programs & Packages" description="Choose your DekiFit coaching package — Basic, Pro, or Elite — from a step-by-step basic plan to premium 1-on-1 video call coaching with Dejan Badzukov. Subscribe via bank transfer.">
        <!-- Hero -->
        <section class="relative pt-32 pb-20 px-6 overflow-hidden">
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
            
            <div class="relative z-10 max-w-4xl mx-auto text-center">
                <p class="text-emerald-400 text-xs uppercase tracking-[0.5em] font-semibold mb-6">Coaching Packages</p>
                <h1 class="font-display text-5xl md:text-7xl text-off-white mb-8 uppercase">
                    <SplitText 
                        text="Choose Your" 
                        :delay="50"
                        class="block"
                    />
                    <span class="text-gradient">
                        <SplitText 
                            text="Package" 
                            :delay="80"
                        />
                    </span>
                </h1>
                <p class="text-text-body text-lg max-w-2xl mx-auto leading-relaxed">
                    Three levels of coaching — from a step-by-step basic plan to premium 1-on-1 video call coaching. Every package includes access to your personal client dashboard.
                </p>
            </div>
        </section>

        <!-- Packages Grid -->
        <section class="py-20 md:py-32 px-6">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <SpotlightCard 
                        v-for="pkg in packagesList" 
                        :key="pkg.title"
                        class="group bg-dark-grey/50 border rounded-2xl overflow-hidden hover:border-emerald-500/30 transition-all duration-500 flex flex-col"
                        :class="pkg.highlight ? 'border-emerald-500/40' : 'border-emerald-500/10'"
                        :spotlight-color="'rgba(242, 185, 12, 0.15)'"
                    >
                        <div v-if="pkg.highlight" class="bg-emerald-500 text-black text-center text-xs uppercase tracking-[0.2em] font-bold py-2">Best Value</div>
                        <div class="p-8 flex flex-col flex-1">
                            <h3 class="font-display text-2xl text-off-white mb-1 group-hover:text-emerald-300 transition-colors uppercase">{{ pkg.title }}</h3>
                            <p class="text-emerald-400 font-display text-4xl mb-1">€{{ pkg.price }}</p>
                            <p class="text-warm-grey text-sm mb-6">{{ pkg.tagline }}</p>

                            <ul class="space-y-3 mb-8 flex-1">
                                <li v-for="feature in pkg.features" :key="feature" class="flex items-start gap-2">
                                    <svg v-if="!isInherit(feature)" class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-sm" :class="isInherit(feature) ? 'text-off-white font-semibold' : 'text-warm-grey'">{{ feature }}</span>
                                </li>
                            </ul>

                            <Link :href="`/checkout?package=${pkg.slug}`" class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-black text-sm tracking-[0.15em] uppercase font-bold rounded-lg text-center hover:from-emerald-400 hover:to-emerald-500 transition-all duration-300">
                                Subscribe
                            </Link>
                        </div>
                    </SpotlightCard>
                </div>
                <p class="text-center text-warm-grey text-sm mt-10 max-w-2xl mx-auto">
                    Payment is by bank transfer. Once your transfer is confirmed, you receive a login to your personal DekiFit client dashboard.
                </p>
            </div>
        </section>

        <!-- Comparison Table -->
        <section class="py-20 md:py-32 px-6 bg-dark-warm/30">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <p class="text-emerald-400 text-xs uppercase tracking-[0.5em] font-semibold mb-4">Compare Packages</p>
                    <h2 class="font-display text-4xl md:text-5xl text-off-white uppercase">
                        <SplitText text="Find Your Fit" tag="span" split-type="words" />
                    </h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[600px]">
                        <thead>
                            <tr class="border-b border-emerald-500/20">
                                <th class="text-left py-4 px-4 text-warm-grey text-sm font-normal">Feature</th>
                                <th class="text-center py-4 px-4 text-emerald-400 text-sm font-semibold">Basic</th>
                                <th class="text-center py-4 px-4 text-emerald-400 text-sm font-semibold">Pro</th>
                                <th class="text-center py-4 px-4 text-emerald-400 text-sm font-semibold">Elite</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in comparisonData" :key="row.feature" class="border-b border-emerald-500/10">
                                <td class="py-4 px-4 text-off-white text-sm">{{ row.feature }}</td>
                                <td class="text-center py-4 px-4">
                                    <span v-if="row.start === true" class="text-emerald-400">✓</span>
                                    <span v-else-if="row.start === false" class="text-warm-grey">—</span>
                                    <span v-else class="text-text-body text-sm">{{ row.start }}</span>
                                </td>
                                <td class="text-center py-4 px-4">
                                    <span v-if="row.pro === true" class="text-emerald-400">✓</span>
                                    <span v-else-if="row.pro === false" class="text-warm-grey">—</span>
                                    <span v-else class="text-text-body text-sm">{{ row.pro }}</span>
                                </td>
                                <td class="text-center py-4 px-4">
                                    <span v-if="row.elite === true" class="text-emerald-400">✓</span>
                                    <span v-else-if="row.elite === false" class="text-warm-grey">—</span>
                                    <span v-else class="text-text-body text-sm">{{ row.elite }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-20 md:py-32 px-6">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="font-display text-4xl md:text-5xl text-off-white mb-6 uppercase">
                    <span class="shiny-text">Not Sure Which Package?</span>
                </h2>
                <p class="text-text-body text-lg mb-10">
                    Message me directly and I'll help you pick the right package for your goals and experience level.
                </p>
                <Link href="/contact" class="inline-block px-12 py-5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-black text-sm tracking-[0.3em] uppercase font-bold rounded-lg hover:from-emerald-400 hover:to-emerald-500 transition-all duration-300 glow-emerald hover:glow-emerald-strong">
                    Ask a Question
                </Link>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Lightfall from '@/components/vue-bits/Lightfall.vue';
import SplitText from '@/components/vue-bits/SplitText.vue';
import SpotlightCard from '@/components/vue-bits/SpotlightCard.vue';
import { fallbackPackages, toPackage, isInherit } from '@/data/packages';

const props = defineProps({
    programs: { type: Array, default: () => [] },
});

const packagesList = props.programs && props.programs.length ? props.programs.map(toPackage) : fallbackPackages;

const comparisonData = [
    { feature: 'Training program', start: true, pro: true, elite: true },
    { feature: 'Exercise video library', start: true, pro: true, elite: true },
    { feature: 'Nutrition plan', start: 'Basic', pro: 'Fully personalized', elite: 'Fully personalized' },
    { feature: 'Calories & macros', start: true, pro: true, elite: true },
    { feature: 'Progress tracking', start: true, pro: true, elite: true },
    { feature: 'Check-ins', start: 'Sunday', pro: 'Weekly + progress review', elite: 'More frequent' },
    { feature: 'Support', start: 'Email / message', pro: 'Direct Instagram', elite: 'Priority response' },
    { feature: 'Video calls', start: false, pro: '1 per month', elite: '2 per month' },
    { feature: 'Training & nutrition adjustments', start: false, pro: true, elite: 'Faster' },
    { feature: 'Goal-specific programming', start: false, pro: true, elite: true },
    { feature: 'Exercise tips and form checks', start: false, pro: true, elite: 'Feedback from your videos' },
    { feature: 'Detailed weekly progress analysis', start: false, pro: false, elite: true },
    { feature: 'Cardio / step recommendations', start: false, pro: false, elite: true },
    { feature: 'Priority scheduling', start: false, pro: false, elite: true },
];
</script>
