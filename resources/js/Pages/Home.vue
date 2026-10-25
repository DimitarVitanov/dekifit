<template>
    <AppLayout :title="page.meta_title" :description="page.meta_description">
        <!-- Hero Section -->
        <section class="relative min-h-screen flex items-center justify-center overflow-hidden">
            <div class="absolute inset-0">
                <Lightfall 
                    :colors="['#f2b90c', '#e8b52c', '#f5f5f5']"
                    background-color="#050505"
                    :speed="0.4"
                    :streak-count="3"
                    :glow="1.5"
                    :density="0.5"
                    class="w-full h-full"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-dark/60 via-dark/40 to-dark"></div>
            </div>
            
            <div class="relative z-10 text-center max-w-5xl mx-auto px-6 pt-24">
                <p class="text-emerald-400 text-xs uppercase tracking-[0.5em] font-semibold mb-6">Train. Focus. Transform.</p>
                
                <h1 class="font-display text-4xl md:text-6xl lg:text-7xl text-off-white leading-tight mb-8 uppercase">
                    <SplitText 
                        text="It's a Marathon," 
                        :delay="50"
                        :threshold="0.2"
                        class="block"
                    />
                    <span class="text-gradient">
                        <SplitText 
                            text="Not a Sprint" 
                            :delay="80"
                            :threshold="0.2"
                        />
                    </span>
                </h1>
                
                <p class="text-text-body text-lg md:text-xl max-w-2xl mx-auto mb-10 leading-relaxed">
                    Fitness plans and bodybuilding programs built for beginners to advanced lifters — designed by coach Dejan Badzukov to build real, lasting results.
                </p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <Link v-magnetic="0.3" href="/programs" class="px-10 py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 text-black text-sm tracking-[0.2em] uppercase font-bold rounded-lg hover:from-emerald-400 hover:to-emerald-500 transition-all duration-300 glow-emerald hover:glow-emerald-strong">
                        View Packages
                    </Link>
                    <Link href="/about" class="px-10 py-4 border border-emerald-500/30 text-emerald-400 text-sm tracking-[0.2em] uppercase font-semibold rounded-lg hover:bg-emerald-500/10 hover:border-emerald-500/50 transition-all duration-300">
                        Meet Your Coach
                    </Link>
                </div>
            </div>
            
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
                <div class="w-6 h-10 border-2 border-emerald-500/40 rounded-full flex justify-center pt-2">
                    <div class="w-1 h-3 bg-emerald-500/60 rounded-full"></div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-16 bg-dark-warm/50 border-y border-emerald-500/10">
            <div class="max-w-6xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-8">
                <Reveal
                    v-for="(stat, i) in stats"
                    :key="stat.value"
                    as="div"
                    class="text-center"
                    :delay="i * 90"
                >
                    <p class="font-display text-emerald-400 text-4xl lg:text-5xl mb-2">
                        <CountUp :value="stat.value" />
                    </p>
                    <p class="text-warm-grey text-xs uppercase tracking-[0.2em]">{{ stat.label }}</p>
                </Reveal>
            </div>
        </section>

        <!-- Bio Teaser -->
        <section class="py-20 md:py-32 px-6">
            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <Reveal as="div" class="relative order-2 lg:order-1">
                    <div v-tilt="5" class="aspect-square rounded-3xl bg-gradient-to-br from-emerald-900/30 to-dark-grey border border-emerald-500/20 overflow-hidden flex items-center justify-center">
                        <LazyVideo src="/videos/dekifit-coach.mp4" poster="/images/dekifit-logo.webp" alt="DekiFit — Dejan Badzukov bodybuilding coach logo" />
                    </div>
                    <div class="absolute -bottom-6 -right-6 w-32 h-32 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 glow-emerald-strong flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-black font-display text-3xl">
                                <CountUp value="10+" />
                            </p>
                            <p class="text-black/80 text-xs uppercase tracking-wider">Years</p>
                        </div>
                    </div>
                </Reveal>
                <Reveal as="div" :delay="120" class="order-1 lg:order-2">
                    <p class="text-emerald-400 text-xs uppercase tracking-[0.5em] font-semibold mb-4">Your Coach</p>
                    <h2 class="font-display text-4xl md:text-5xl text-off-white mb-8 leading-tight uppercase">
                        <SplitText text="Meet" tag="span" split-type="words" class="block" />
                        <span class="text-gradient block">
                            <SplitText text="Dejan Badzukov" tag="span" split-type="words" :delay="80" />
                        </span>
                    </h2>
                    <div class="space-y-6 text-text-body leading-relaxed">
                        <p>
                            With over 10 years of dedicated lifting and strength training experience, I've built my career around helping people achieve their fitness goals through proven training methods, nutrition plans, consistency, and personalized coaching.
                        </p>
                        <p>
                            My background in Physical Education provides a strong foundation in exercise science, human movement, and athletic performance — combined with real-world coaching experience to create effective programs focused on strength, injury prevention, and sustainable progress.
                        </p>
                    </div>
                    <Link href="/about" class="inline-flex items-center gap-2 mt-8 text-emerald-400 text-sm font-semibold hover:gap-3 transition-all">
                        Read My Full Story
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </Link>
                </Reveal>
            </div>
        </section>

        <!-- Transformations -->
        <section class="py-20 md:py-32 bg-dark-warm/30 overflow-hidden">
            <div class="max-w-6xl mx-auto px-6">
                <Reveal as="div" class="text-center mb-14">
                    <p class="text-emerald-400 text-xs uppercase tracking-[0.5em] font-semibold mb-4">Results</p>
                    <h2 class="font-display text-4xl md:text-5xl text-off-white mb-6 uppercase">
                        <SplitText text="Client" tag="span" split-type="words" /> <span class="text-gradient"><SplitText text="Transformations" tag="span" split-type="words" :delay="80" /></span>
                    </h2>
                    <p class="text-text-body text-lg max-w-2xl mx-auto">
                        Real clients, real progress — built with consistent training, a plan that fits their life, and a coach in their corner.
                    </p>
                </Reveal>
            </div>
            <!-- Full-bleed: the card row runs edge to edge and fades out at both sides. -->
            <Reveal as="div">
                <TransformationGallery :photos="transformations" />
            </Reveal>
            <p class="text-center text-warm-grey text-sm mt-8 px-6">
                Drag or swipe to browse, tap a photo to see it full size.
                <a href="#packages" class="text-emerald-400 font-semibold hover:text-emerald-300 whitespace-nowrap">Start your transformation →</a>
            </p>
        </section>

        <!-- Packages Section -->
        <section id="packages" class="py-20 md:py-32 px-6 scroll-mt-20">
            <div class="max-w-6xl mx-auto">
                <Reveal as="div" class="text-center mb-16">
                    <p class="text-emerald-400 text-xs uppercase tracking-[0.5em] font-semibold mb-4">Choose Your Package</p>
                    <h2 class="font-display text-4xl md:text-5xl text-off-white mb-6 uppercase">
                        <SplitText text="Coaching Packages" tag="span" split-type="words" />
                    </h2>
                    <p class="text-text-body text-lg max-w-2xl mx-auto">
                        Three levels of coaching — from a step-by-step basic plan to premium 1-on-1 video call coaching. Pick how involved you want your coach to be.
                    </p>
                </Reveal>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Reveal
                        v-for="(pkg, i) in packages"
                        :key="pkg.title"
                        as="div"
                        class="h-full"
                        :delay="i * 110"
                    >
                        <SpotlightCard
                            v-tilt="4"
                            class="group h-full bg-dark-grey/50 border rounded-2xl p-8 hover:border-emerald-500/30 transition-[border-color,box-shadow,background-color] duration-500 flex flex-col"
                            :class="pkg.highlight ? 'border-emerald-500/40' : 'border-emerald-500/10'"
                            :spotlight-color="'rgba(242, 185, 12, 0.15)'"
                        >
                            <p v-if="pkg.highlight" class="self-start mb-4 px-3 py-1 rounded-full bg-emerald-500 text-black text-[10px] uppercase tracking-[0.2em] font-bold">Best Value</p>
                            <h3 class="font-display text-2xl text-off-white mb-1 group-hover:text-emerald-300 transition-colors uppercase">{{ pkg.title }}</h3>
                            <p class="text-emerald-400 font-display text-3xl mb-1">€{{ pkg.price }}</p>
                            <p class="text-warm-grey text-sm mb-6">{{ pkg.tagline }}</p>
                            <ul class="space-y-2 mb-8 flex-1">
                                <li v-for="feature in pkg.features" :key="feature" class="flex items-start gap-2 text-sm" :class="isInherit(feature) ? 'text-off-white font-semibold' : 'text-warm-grey'">
                                    <svg v-if="!isInherit(feature)" class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ feature }}
                                </li>
                            </ul>
                            <Link :href="`/checkout?package=${pkg.slug}`" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-black text-sm tracking-[0.15em] uppercase font-bold rounded-lg hover:from-emerald-400 hover:to-emerald-500 transition-all duration-300">
                                Subscribe
                            </Link>
                        </SpotlightCard>
                    </Reveal>
                </div>
                <p class="text-center text-warm-grey text-sm mt-10">
                    Subscribe via bank transfer — once payment is confirmed, you get a login to your personal client dashboard.
                </p>
            </div>
        </section>

        <!-- Nutrition Philosophy -->
        <section class="py-20 md:py-32 px-6 bg-dark-warm/30">
            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <Reveal as="div">
                    <p class="text-emerald-400 text-xs uppercase tracking-[0.5em] font-semibold mb-4">Nutrition</p>
                    <h2 class="font-display text-4xl md:text-5xl text-off-white mb-8 leading-tight uppercase">
                        <SplitText text="Eat Well." tag="span" split-type="words" class="block" />
                        <span class="text-gradient block">
                            <SplitText text="Enjoy The Process." tag="span" split-type="words" :delay="80" />
                        </span>
                    </h2>
                    <div class="space-y-6 text-text-body leading-relaxed">
                        <p>
                            Every program comes with a nutrition plan designed to be flexible — meals can be chosen freely without a strict order, so you avoid monotony while still hitting your targets.
                        </p>
                        <p>
                            I don't like strictly counting calories or weighing food, because it can create an unhealthy relationship with nutrition. By following the plan, daily intake roughly ranges between 2300–3000 kcal for steady, sustainable muscle gain.
                        </p>
                    </div>
                    <Link href="/nutrition" class="inline-flex items-center gap-2 mt-8 text-emerald-400 text-sm font-semibold hover:gap-3 transition-all">
                        What's in the Nutrition Plan
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </Link>
                </Reveal>
                <Reveal as="div" :delay="120" class="relative">
                    <div v-tilt="5" class="aspect-square rounded-3xl bg-gradient-to-br from-emerald-900/30 to-dark-grey border border-emerald-500/20 overflow-hidden">
                        <Particles
                            :particleCount="60"
                            :particleSpread="8"
                            :speed="0.3"
                            :particleColors="['#f2b90c', '#e8b52c', '#f5f5f5']"
                            :sizeRandomness="1"
                            :moveParticlesOnHover="true"
                            class="w-full h-full"
                        />
                    </div>
                </Reveal>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="relative py-24 md:py-40 px-6 overflow-hidden">
            <div class="absolute inset-0">
                <Lightfall 
                    :colors="['#f2b90c', '#e8b52c', '#f5f5f5']"
                    background-color="#050505"
                    :speed="0.3"
                    :streak-count="2"
                    :glow="1"
                    :opacity="0.5"
                    class="w-full h-full"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-dark via-dark/80 to-dark/60"></div>
            </div>
            
            <Reveal as="div" class="relative z-10 max-w-3xl mx-auto text-center">
                <h2 class="font-display text-4xl md:text-6xl text-off-white mb-6 uppercase">
                    <span class="shiny-text">Ready to Transform?</span>
                </h2>
                <p class="text-text-body text-lg mb-10 max-w-xl mx-auto">
                    Pick your package, transfer the fee via bank transfer, and get your personalized program and nutrition plan sent straight to you.
                </p>
                <Link v-magnetic="0.3" href="/programs" class="inline-block px-12 py-5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-black text-sm tracking-[0.3em] uppercase font-bold rounded-lg hover:from-emerald-400 hover:to-emerald-500 transition-all duration-300 glow-emerald hover:glow-emerald-strong">
                    Get Your Program
                </Link>
            </Reveal>
        </section>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Lightfall from '@/components/vue-bits/Lightfall.vue';
import SplitText from '@/components/vue-bits/SplitText.vue';
import Particles from '@/components/vue-bits/Particles.vue';
import SpotlightCard from '@/components/vue-bits/SpotlightCard.vue';
import Reveal from '@/components/Reveal.vue';
import CountUp from '@/components/CountUp.vue';
import LazyVideo from '@/components/LazyVideo.vue';
import TransformationGallery from '@/components/TransformationGallery.vue';
import { fallbackPackages, toPackage, isInherit } from '@/data/packages';

const props = defineProps({
    programs: { type: Array, default: () => [] },
    page: { type: Object, default: () => ({ meta_title: 'DekiFit — Train. Focus. Transform.', meta_description: 'Personalized bodybuilding programs and nutrition plans by coach Dejan Badzukov. Build muscle, lose fat, and transform your physique.' }) },
});

const stats = [
    { value: '10+', label: 'Years Coaching' },
    { value: '12', label: 'Week Programs' },
    { value: '3', label: 'Package Tiers' },
    { value: '100%', label: 'Personalized' },
];

const packages = props.programs.length ? props.programs.map(toPackage) : fallbackPackages;

// Client before/after collages (public/images/transformations), faces blurred. No. 5 is the
// landscape one, kept mid-row so the slider opens on portrait cards.
const transformations = [1, 2, 3, 4, 6, 5, 7].map(n => ({
    src: `/images/transformations/transformation-${n}`,
    width: 960,
    height: n === 5 ? 868 : 1707,
}));
</script>
