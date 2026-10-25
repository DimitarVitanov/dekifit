<template>
    <AppLayout title="About Dejan Badzukov" description="Meet Dejan Badzukov, the coach behind DekiFit — 10+ years of lifting and strength training experience helping clients build muscle and transform their physiques.">
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
                <p class="text-emerald-400 text-xs uppercase tracking-[0.5em] font-semibold mb-6">Our Story</p>
                <h1 class="font-display text-5xl md:text-7xl text-off-white mb-8 uppercase">
                    <SplitText 
                        text="Train. Focus." 
                        :delay="50"
                        class="block"
                    />
                    <span class="text-gradient">
                        <SplitText 
                            text="Transform." 
                            :delay="80"
                        />
                    </span>
                </h1>
                <p class="text-text-body text-lg max-w-2xl mx-auto leading-relaxed">
                    DekiFit is the personal training and nutrition brand of coach Dejan Badzukov — built on real coaching experience, not shortcuts.
                </p>
            </div>
        </section>

        <!-- Bio -->
        <section class="py-20 md:py-32 px-6">
            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="relative">
                    <div class="aspect-[4/5] rounded-3xl bg-gradient-to-br from-emerald-900/30 to-dark-grey border border-emerald-500/20 overflow-hidden flex items-center justify-center">
                        <img src="/images/dekifit-logo.webp" alt="Dejan Badzukov — DekiFit bodybuilding coach" width="480" height="600" loading="lazy" class="w-2/3 h-auto object-contain">
                    </div>
                </div>
                
                <div>
                    <p class="text-emerald-400 text-xs uppercase tracking-[0.5em] font-semibold mb-4">Meet Your Coach</p>
                    <h2 class="font-display text-4xl md:text-5xl text-off-white mb-8 leading-tight uppercase">
                        <SplitText text="Dejan" tag="span" split-type="words" class="block" />
                        <span class="text-gradient block">
                            <SplitText text="Badzukov" tag="span" split-type="words" :delay="80" />
                        </span>
                    </h2>
                    <div class="space-y-6 text-text-body leading-relaxed">
                        <p>
                            With over 10 years of dedicated lifting and strength training experience, I have built my career around helping people achieve their fitness goals through proven training methods, nutrition plans, consistency, and personalized coaching. My passion for fitness extends beyond the gym, allowing me to guide clients of all fitness levels toward lasting results and a healthier lifestyle.
                        </p>
                        <p>
                            My background in Physical Education provides a strong foundation in exercise science, human movement, and athletic performance. By combining academic knowledge with real-world coaching experience, I create effective training programs that focus on strength development, injury prevention, improved performance, and sustainable progress.
                        </p>
                        <p>
                            Nothing is more rewarding than seeing my clients succeed. Over the years, I have helped numerous individuals transform their bodies, increase their confidence, and develop lifelong healthy habits. From beginners taking their first steps into fitness to experienced athletes seeking the next level of performance, I am committed to delivering professional support and results-driven coaching every step of the way.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Values -->
        <section class="py-20 md:py-32 px-6 bg-dark-warm/30">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <p class="text-emerald-400 text-xs uppercase tracking-[0.5em] font-semibold mb-4">What I Stand For</p>
                    <h2 class="font-display text-4xl md:text-5xl text-off-white uppercase">
                        <SplitText text="Core Principles" tag="span" split-type="words" />
                    </h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <SpotlightCard 
                        v-for="value in values" 
                        :key="value.title"
                        class="bg-dark-grey/50 border border-emerald-500/10 rounded-2xl p-8 text-center"
                        :spotlight-color="'rgba(242, 185, 12, 0.15)'"
                    >
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500/20 to-emerald-600/20 flex items-center justify-center mx-auto mb-6">
                            <span class="text-emerald-400 text-2xl">{{ value.icon }}</span>
                        </div>
                        <h3 class="font-display text-xl text-off-white mb-3 uppercase">{{ value.title }}</h3>
                        <p class="text-text-body text-sm leading-relaxed">{{ value.description }}</p>
                    </SpotlightCard>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-20 md:py-32 px-6">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="font-display text-4xl md:text-5xl text-off-white mb-6 uppercase">
                    <span class="shiny-text">Ready to Start?</span>
                </h2>
                <p class="text-text-body text-lg mb-10">
                    Choose your package and start your transformation with personalized coaching from Dejan.
                </p>
                <Link href="/programs" class="inline-block px-12 py-5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-black text-sm tracking-[0.3em] uppercase font-bold rounded-lg hover:from-emerald-400 hover:to-emerald-500 transition-all duration-300 glow-emerald hover:glow-emerald-strong">
                    View Packages
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

const values = [
    {
        icon: '💪',
        title: 'Consistency',
        description: 'Real transformation comes from steady, dedicated work over 12 weeks — not shortcuts.',
    },
    {
        icon: '🎯',
        title: 'Personalized',
        description: 'Every program and nutrition plan is tailored to your body, goals, and experience level.',
    },
    {
        icon: '🥗',
        title: 'Balanced Nutrition',
        description: 'Enjoy the process. Eat balanced food you like, without unnecessary restriction.',
    },
    {
        icon: '📈',
        title: 'Results-Driven',
        description: 'Weekly or daily check-ins keep you accountable and on track toward your goal.',
    },
];
</script>
