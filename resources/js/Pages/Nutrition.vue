<template>
    <AppLayout title="Nutrition Plan" description="What's inside the DekiFit nutrition plan for muscle building: 16 flexible recipes at 2300–3000 kcal a day by coach Dejan Badzukov. Included in every coaching package.">
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
                <p class="text-emerald-400 text-xs uppercase tracking-[0.5em] font-semibold mb-6">Included in every package</p>
                <h1 class="font-display text-5xl md:text-7xl text-off-white mb-8 uppercase">
                    <SplitText
                        text="Nutrition"
                        :delay="50"
                        class="block"
                    />
                    <span class="text-gradient">
                        <SplitText
                            text="Made Simple"
                            :delay="80"
                        />
                    </span>
                </h1>
                <p class="text-text-body text-lg max-w-2xl mx-auto leading-relaxed">
                    Every DekiFit package includes a nutrition plan for muscle mass building, weight maintenance, and a healthy relationship with food. Meals can be chosen freely — no strict order, no calorie obsession.
                </p>
            </div>
        </section>

        <!-- What's inside -->
        <section class="py-20 md:py-28 px-6">
            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <Reveal as="div" class="relative">
                    <div v-tilt="4" class="relative aspect-[4/5] max-w-md mx-auto rounded-3xl overflow-hidden border border-emerald-500/20">
                        <img src="/images/nutrition-plan.webp" alt="A seasoned steak on a wooden board" width="619" height="920" loading="lazy" class="absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-dark via-dark/20 to-transparent"></div>
                        <div class="absolute bottom-0 inset-x-0 p-6">
                            <p class="text-emerald-400 text-xs uppercase tracking-[0.3em] font-semibold mb-1">Main Gain</p>
                            <p class="font-display text-2xl text-off-white uppercase">Nutrition plan</p>
                            <p class="text-warm-grey text-sm">by coach Dejan Badzukov</p>
                        </div>
                    </div>
                </Reveal>
                <Reveal as="div" :delay="120">
                    <p class="text-emerald-400 text-xs uppercase tracking-[0.5em] font-semibold mb-4">What's inside</p>
                    <h2 class="font-display text-4xl md:text-5xl text-off-white mb-8 leading-tight uppercase">
                        <SplitText text="Your Full Plan," tag="span" split-type="words" class="block" />
                        <span class="text-gradient block">
                            <SplitText text="In Your Dashboard" tag="span" split-type="words" :delay="80" />
                        </span>
                    </h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div v-for="item in inside" :key="item.label" class="bg-dark-grey/50 border border-emerald-500/10 rounded-2xl p-5">
                            <p class="font-display text-2xl sm:text-3xl text-off-white whitespace-nowrap">{{ item.value }}</p>
                            <p class="text-warm-grey text-sm mt-1">{{ item.label }}</p>
                        </div>
                    </div>
                    <p class="text-text-body leading-relaxed mt-6">
                        Every recipe comes with ingredients, amounts and macros. Subscribers find the full plan in their client dashboard, next to their training program and exercise videos.
                    </p>
                </Reveal>
            </div>
        </section>

        <!-- The menu: a taste, the rest locked -->
        <section class="py-20 md:py-28 px-6 bg-dark-warm/30">
            <div class="max-w-6xl mx-auto">
                <Reveal as="div" class="text-center mb-14">
                    <p class="text-emerald-400 text-xs uppercase tracking-[0.5em] font-semibold mb-4">The Menu</p>
                    <h2 class="font-display text-4xl md:text-5xl text-off-white uppercase">
                        <SplitText text="A Taste Of What's Inside" tag="span" split-type="words" />
                    </h2>
                    <p class="text-text-body text-lg max-w-2xl mx-auto mt-6">One recipe on the house. The rest unlock with your subscription.</p>
                </Reveal>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Free sample -->
                    <Reveal as="div" class="h-full">
                        <SpotlightCard class="h-full bg-dark-grey/60 border border-emerald-500/40 rounded-2xl p-6 flex flex-col" :spotlight-color="'rgba(242, 185, 12, 0.15)'">
                            <p class="self-start mb-4 px-3 py-1 rounded-full bg-emerald-500 text-black text-[10px] uppercase tracking-[0.2em] font-bold">Free sample</p>
                            <p class="text-emerald-400 text-xs uppercase tracking-wider mb-1">{{ sample.group }}</p>
                            <h3 class="font-display text-2xl text-off-white uppercase mb-4">{{ sample.name }}</h3>
                            <ul class="space-y-2 mb-5 flex-1">
                                <li v-for="item in sample.ingredients" :key="item" class="flex items-start gap-2 text-sm text-text-body">
                                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    {{ item }}
                                </li>
                            </ul>
                            <p class="text-emerald-400 text-xs font-semibold tracking-wider border-t border-emerald-500/10 pt-4">{{ sample.macros }}</p>
                        </SpotlightCard>
                    </Reveal>

                    <!-- Locked groups: names only, details stay in the dashboard -->
                    <Reveal v-for="(group, i) in lockedGroups" :key="group.title" as="div" class="h-full" :delay="(i + 1) * 90">
                        <div class="h-full bg-dark-grey/40 border border-emerald-500/10 rounded-2xl p-6 flex flex-col">
                            <div class="flex items-center justify-between gap-3 mb-5">
                                <h3 class="font-display text-xl text-emerald-400 uppercase">{{ group.title }}</h3>
                                <span class="text-warm-grey text-xs">{{ group.count }} {{ group.count === 1 ? 'option' : 'options' }}</span>
                            </div>
                            <ul class="space-y-3 flex-1">
                                <li v-for="name in group.names" :key="name" class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-warm-grey flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                                    <span class="min-w-0 flex-1">
                                        <span class="block text-off-white text-sm truncate">{{ name }}</span>
                                        <span class="flex gap-1.5 mt-1.5" aria-hidden="true">
                                            <span class="h-1.5 w-10 rounded-full bg-white/10"></span>
                                            <span class="h-1.5 w-6 rounded-full bg-white/10"></span>
                                            <span class="h-1.5 w-8 rounded-full bg-white/10"></span>
                                        </span>
                                    </span>
                                </li>
                                <li v-if="group.count > group.names.length" class="text-warm-grey text-sm pl-7">+ {{ group.count - group.names.length }} more</li>
                            </ul>
                        </div>
                    </Reveal>
                </div>

                <!-- Unlock -->
                <Reveal as="div" class="mt-10">
                    <div class="relative overflow-hidden rounded-3xl border border-emerald-500/30 bg-gradient-to-br from-emerald-900/30 via-dark-grey to-dark-grey p-8 md:p-10 flex flex-col md:flex-row md:items-center gap-6">
                        <span class="w-14 h-14 rounded-2xl bg-emerald-500/15 text-emerald-400 flex items-center justify-center flex-shrink-0">
                            <svg v-if="member" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                            <svg v-else class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                        </span>
                        <div class="flex-1">
                            <template v-if="member">
                                <h3 class="font-display text-2xl md:text-3xl text-off-white uppercase">Your plan is ready</h3>
                                <p class="text-text-body mt-2">Every recipe with ingredients, amounts and macros is waiting in your dashboard.</p>
                            </template>
                            <template v-else>
                                <h3 class="font-display text-2xl md:text-3xl text-off-white uppercase">Unlock the full plan</h3>
                                <p class="text-text-body mt-2">All recipes with ingredients, amounts and macros, plus snack ideas and food swap lists. Included with every package.</p>
                            </template>
                        </div>
                        <div class="flex flex-col sm:flex-row md:flex-col lg:flex-row gap-3 flex-shrink-0">
                            <Link v-if="member" :href="member.href" class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 text-black text-sm tracking-[0.15em] uppercase font-bold rounded-lg hover:from-emerald-400 hover:to-emerald-500 transition-all duration-300">
                                {{ member.label }}
                            </Link>
                            <template v-else>
                                <a href="#packages" class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 text-black text-sm tracking-[0.15em] uppercase font-bold rounded-lg hover:from-emerald-400 hover:to-emerald-500 transition-all duration-300">
                                    See packages
                                </a>
                                <Link href="/login" class="inline-flex items-center justify-center px-8 py-4 border border-emerald-500/30 text-off-white text-sm tracking-[0.15em] uppercase font-bold rounded-lg hover:border-emerald-500/60 transition-colors">
                                    Client login
                                </Link>
                            </template>
                        </div>
                    </div>
                </Reveal>
            </div>
        </section>

        <!-- Philosophy -->
        <section class="py-20 md:py-32 px-6">
            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <p class="text-emerald-400 text-xs uppercase tracking-[0.5em] font-semibold mb-4">Philosophy</p>
                    <h2 class="font-display text-4xl md:text-5xl text-off-white mb-8 leading-tight uppercase">
                        <SplitText text="Enjoy Every" tag="span" split-type="words" class="block" />
                        <span class="text-gradient block">
                            <SplitText text="Delicious Bite" tag="span" split-type="words" :delay="80" />
                        </span>
                    </h2>
                    <div class="space-y-6 text-text-body leading-relaxed">
                        <p>
                            This plan is designed to be flexible for everyone's taste — meals can be chosen freely without a strict order or regime, and they're quick and easy to prepare.
                        </p>
                        <p>
                            I personally don't like strictly counting calories or weighing food, because it can create an unhealthy approach to nutrition. By following any of the listed recipes throughout the day, daily intake roughly ranges between 2300–3000 kcal.
                        </p>
                        <p>
                            This plan is meant to complement regular physical activity and training, which are essential for proper muscle gain.
                        </p>
                    </div>
                </div>
                <div class="relative">
                    <div class="aspect-square rounded-3xl bg-gradient-to-br from-emerald-900/30 to-dark-grey border border-emerald-500/20 overflow-hidden">
                        <Particles
                            :particleCount="60"
                            :particleSpread="8"
                            :speed="0.3"
                            :particleColors="['#f2b90c', '#e8b52c', '#f5f5f5']"
                            :sizeRandomness="1"
                            class="w-full h-full"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- Packages -->
        <section id="packages" class="py-20 md:py-28 px-6 bg-dark-warm/30 scroll-mt-20">
            <div class="max-w-6xl mx-auto">
                <Reveal as="div" class="text-center mb-14">
                    <p class="text-emerald-400 text-xs uppercase tracking-[0.5em] font-semibold mb-4">Subscribe</p>
                    <h2 class="font-display text-4xl md:text-5xl text-off-white uppercase">
                        <span class="shiny-text">Get Your Plan</span>
                    </h2>
                    <p class="text-text-body text-lg max-w-2xl mx-auto mt-6">The nutrition plan comes with every package, together with your training program and the exercise video library.</p>
                </Reveal>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Reveal v-for="(pkg, i) in packages" :key="pkg.slug" as="div" class="h-full" :delay="i * 110">
                        <SpotlightCard
                            class="group h-full bg-dark-grey/50 border rounded-2xl p-8 hover:border-emerald-500/30 transition-[border-color] duration-500 flex flex-col"
                            :class="pkg.highlight ? 'border-emerald-500/40' : 'border-emerald-500/10'"
                            :spotlight-color="'rgba(242, 185, 12, 0.15)'"
                        >
                            <p v-if="pkg.highlight" class="self-start mb-4 px-3 py-1 rounded-full bg-emerald-500 text-black text-[10px] uppercase tracking-[0.2em] font-bold">Best Value</p>
                            <h3 class="font-display text-2xl text-off-white mb-1 uppercase">{{ pkg.title }}</h3>
                            <p class="text-emerald-400 font-display text-3xl mb-1">€{{ pkg.price }}</p>
                            <p class="text-warm-grey text-sm mb-6">{{ pkg.tagline }}</p>
                            <ul class="space-y-2 mb-8 flex-1">
                                <li v-for="line in nutritionLines(pkg)" :key="line" class="flex items-start gap-2 text-sm" :class="isInherit(line) ? 'text-off-white font-semibold' : 'text-warm-grey'">
                                    <svg v-if="!isInherit(line)" class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    {{ line }}
                                </li>
                            </ul>
                            <Link :href="`/checkout?package=${pkg.slug}`" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-black text-sm tracking-[0.15em] uppercase font-bold rounded-lg hover:from-emerald-400 hover:to-emerald-500 transition-all duration-300">
                                Subscribe
                            </Link>
                        </SpotlightCard>
                    </Reveal>
                </div>
                <p class="text-center text-warm-grey text-sm mt-10">
                    Subscribe via bank transfer — once payment is confirmed, you get a login to your client dashboard with the full plan.
                    <Link href="/programs" class="text-emerald-400 font-semibold hover:text-emerald-300 whitespace-nowrap">Compare packages →</Link>
                </p>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Lightfall from '@/components/vue-bits/Lightfall.vue';
import SplitText from '@/components/vue-bits/SplitText.vue';
import Particles from '@/components/vue-bits/Particles.vue';
import SpotlightCard from '@/components/vue-bits/SpotlightCard.vue';
import Reveal from '@/components/Reveal.vue';
import { fallbackPackages, isInherit, toPackage } from '@/data/packages';

const props = defineProps({
    programs: { type: Array, default: () => [] },
});

const packages = props.programs.length ? props.programs.map(toPackage) : fallbackPackages;

// What each package adds on nutrition, after "Everything in …, plus:" where it has one.
const nutritionLines = pkg => [
    pkg.features.find(isInherit),
    pkg.features.find(feature => !isInherit(feature) && /nutrition/i.test(feature)) ?? 'Nutrition plan included',
].filter(Boolean);

// Signed-in visitors go straight to their plan instead of the sales pitch.
const member = computed(() => {
    const user = usePage().props.auth?.user;
    if (!user) return null;
    return user.is_admin
        ? { href: '/admin/nutrition', label: 'Manage nutrition plans' }
        : { href: '/portal/nutrition', label: 'Open my plan' };
});

const inside = [
    { value: '16', label: 'Recipes for breakfast, lunch & dinner' },
    { value: '7', label: 'Snack ideas' },
    { value: '2300–3000', label: 'kcal a day, for muscle gain' },
    { value: '4', label: 'Food swap lists' },
];

// The only recipe shown in full on the public site.
const sample = {
    group: 'Breakfast',
    name: 'Bodybuilder Oats',
    ingredients: ['100 g oats', '1 scoop protein powder', 'Banana or frozen fruit', 'Cocoa powder', 'Stevia', 'Peanut butter (optional)'],
    macros: 'P 40 g · C 100 g · F 16 g · 686 kcal',
};

// Names only; ingredients, amounts and macros live in the client dashboard.
const lockedGroups = [
    { title: 'Breakfast', count: 4, names: ['Classic', '"Baki" Shake', 'Anabolic Pancakes', 'French Toast'] },
    { title: 'Lunch', count: 7, names: ['Standard Bodybuilder Meal', 'Loaded Tortillas', 'Chicken Burger "Deki Style"', 'Sweet & Chili Meal'] },
    { title: 'Dinner', count: 4, names: ['Mushroom Trip', 'Tuna Salad', 'Dejan Pasta'] },
];
</script>
