<template>
    <AppLayout title="Get Started" description="Begin your transformation with a personalized nutrition plan from BADJUKOF.">
        <!-- Hero -->
        <section class="relative pt-32 pb-16 px-6 overflow-hidden">
            <div class="absolute inset-0">
                <Lightfall 
                    :colors="['#10b981', '#34d399', '#6ee7b7']"
                    background-color="#0a0a0a"
                    :speed="0.4"
                    :streak-count="3"
                    :glow="1.5"
                    :opacity="0.5"
                    class="w-full h-full"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-dark/70 to-dark"></div>
            </div>
            
            <div class="relative z-10 max-w-4xl mx-auto text-center">
                <p class="text-emerald-400 text-xs uppercase tracking-[0.5em] font-semibold mb-6">Start Your Journey</p>
                <h1 class="font-display text-5xl md:text-6xl text-off-white mb-6">
                    <SplitText 
                        text="Transform Your Physique" 
                        :delay="50"
                        animation-from="{ opacity: 0, y: 40 }"
                        animation-to="{ opacity: 1, y: 0 }"
                    />
                </h1>
                <p class="text-text-body text-lg max-w-xl mx-auto">
                    Tell us about your goals and we'll create a personalized nutrition strategy designed for your success.
                </p>
            </div>
        </section>

        <!-- Application Form -->
        <section class="py-16 md:py-24 px-6">
            <div class="max-w-3xl mx-auto">
                <form @submit.prevent="submit" class="space-y-12">
                    <!-- Step 1: Goals -->
                    <div class="bg-dark-grey/30 border border-emerald-500/10 rounded-2xl p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center text-white font-semibold">1</div>
                            <h2 class="font-display text-2xl text-off-white">Your Goals</h2>
                        </div>
                        
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm text-warm-grey mb-3">What's your primary goal? *</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <label 
                                        v-for="goal in goals" 
                                        :key="goal.value"
                                        class="relative flex items-center gap-4 p-4 bg-dark-grey/50 border rounded-xl cursor-pointer transition-all duration-300"
                                        :class="form.goal === goal.value ? 'border-emerald-500 bg-emerald-500/10' : 'border-emerald-500/20 hover:border-emerald-500/40'"
                                    >
                                        <input type="radio" v-model="form.goal" :value="goal.value" class="sr-only">
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                            :class="form.goal === goal.value ? 'border-emerald-500 bg-emerald-500' : 'border-warm-grey'">
                                            <div v-if="form.goal === goal.value" class="w-2 h-2 rounded-full bg-white"></div>
                                        </div>
                                        <div>
                                            <p class="text-off-white font-medium">{{ goal.label }}</p>
                                            <p class="text-warm-grey text-xs">{{ goal.description }}</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            <div>
                                <label for="timeline" class="block text-sm text-warm-grey mb-2">Target timeline</label>
                                <select 
                                    id="timeline"
                                    v-model="form.timeline"
                                    class="w-full px-4 py-3 bg-dark-grey/50 border border-emerald-500/20 rounded-xl text-off-white focus:border-emerald-500/50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                >
                                    <option value="">Select timeline</option>
                                    <option value="8-weeks">8 weeks</option>
                                    <option value="12-weeks">12 weeks</option>
                                    <option value="16-weeks">16 weeks</option>
                                    <option value="20-weeks">20+ weeks</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: About You -->
                    <div class="bg-dark-grey/30 border border-emerald-500/10 rounded-2xl p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center text-white font-semibold">2</div>
                            <h2 class="font-display text-2xl text-off-white">About You</h2>
                        </div>
                        
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
                            <div>
                                <label for="age" class="block text-sm text-warm-grey mb-2">Age</label>
                                <input 
                                    id="age"
                                    v-model="form.age"
                                    type="number"
                                    min="16"
                                    max="100"
                                    class="w-full px-4 py-3 bg-dark-grey/50 border border-emerald-500/20 rounded-xl text-off-white placeholder-warm-grey focus:border-emerald-500/50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                    placeholder="25"
                                >
                            </div>
                            <div>
                                <label for="gender" class="block text-sm text-warm-grey mb-2">Gender</label>
                                <select 
                                    id="gender"
                                    v-model="form.gender"
                                    class="w-full px-4 py-3 bg-dark-grey/50 border border-emerald-500/20 rounded-xl text-off-white focus:border-emerald-500/50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                >
                                    <option value="">Select</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Current Stats -->
                    <div class="bg-dark-grey/30 border border-emerald-500/10 rounded-2xl p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center text-white font-semibold">3</div>
                            <h2 class="font-display text-2xl text-off-white">Current Stats</h2>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div>
                                <label for="weight" class="block text-sm text-warm-grey mb-2">Current Weight (lbs)</label>
                                <input 
                                    id="weight"
                                    v-model="form.weight"
                                    type="number"
                                    class="w-full px-4 py-3 bg-dark-grey/50 border border-emerald-500/20 rounded-xl text-off-white placeholder-warm-grey focus:border-emerald-500/50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                    placeholder="180"
                                >
                            </div>
                            <div>
                                <label for="height" class="block text-sm text-warm-grey mb-2">Height (inches)</label>
                                <input 
                                    id="height"
                                    v-model="form.height"
                                    type="number"
                                    class="w-full px-4 py-3 bg-dark-grey/50 border border-emerald-500/20 rounded-xl text-off-white placeholder-warm-grey focus:border-emerald-500/50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                    placeholder="70"
                                >
                            </div>
                            <div>
                                <label for="bodyfat" class="block text-sm text-warm-grey mb-2">Est. Body Fat %</label>
                                <input 
                                    id="bodyfat"
                                    v-model="form.bodyfat"
                                    type="number"
                                    class="w-full px-4 py-3 bg-dark-grey/50 border border-emerald-500/20 rounded-xl text-off-white placeholder-warm-grey focus:border-emerald-500/50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                    placeholder="15"
                                >
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label for="experience" class="block text-sm text-warm-grey mb-2">Training Experience</label>
                            <select 
                                id="experience"
                                v-model="form.experience"
                                class="w-full px-4 py-3 bg-dark-grey/50 border border-emerald-500/20 rounded-xl text-off-white focus:border-emerald-500/50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all"
                            >
                                <option value="">Select experience level</option>
                                <option value="beginner">Beginner (0-1 years)</option>
                                <option value="intermediate">Intermediate (1-3 years)</option>
                                <option value="advanced">Advanced (3-5 years)</option>
                                <option value="elite">Elite (5+ years)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Step 4: Additional Info -->
                    <div class="bg-dark-grey/30 border border-emerald-500/10 rounded-2xl p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center text-white font-semibold">4</div>
                            <h2 class="font-display text-2xl text-off-white">Additional Info</h2>
                        </div>
                        
                        <div>
                            <label for="message" class="block text-sm text-warm-grey mb-2">Tell us more about your goals and any dietary restrictions</label>
                            <textarea 
                                id="message"
                                v-model="form.message"
                                rows="4"
                                class="w-full px-4 py-3 bg-dark-grey/50 border border-emerald-500/20 rounded-xl text-off-white placeholder-warm-grey focus:border-emerald-500/50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all resize-none"
                                placeholder="Any allergies, food preferences, competition dates, or specific goals..."
                            ></textarea>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="text-center">
                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="px-16 py-5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm tracking-[0.3em] uppercase font-semibold rounded-xl hover:from-emerald-400 hover:to-teal-500 transition-all duration-300 glow-emerald hover:glow-emerald-strong disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ form.processing ? 'Submitting...' : 'Submit Application' }}
                        </button>
                        <p class="text-warm-grey text-sm mt-4">We'll review your application and get back to you within 24 hours.</p>
                    </div>
                </form>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Lightfall from '@/components/vue-bits/Lightfall.vue';
import SplitText from '@/components/vue-bits/SplitText.vue';

const goals = [
    { value: 'muscle-gain', label: 'Build Muscle', description: 'Gain lean mass' },
    { value: 'fat-loss', label: 'Lose Fat', description: 'Get shredded' },
    { value: 'competition', label: 'Competition Prep', description: 'Stage ready' },
    { value: 'maintenance', label: 'Maintain', description: 'Stay lean' },
];

const form = useForm({
    goal: '',
    timeline: '',
    name: '',
    email: '',
    age: '',
    gender: '',
    weight: '',
    height: '',
    bodyfat: '',
    experience: '',
    message: '',
});

function submit() {
    form.post('/get-started', {
        preserveScroll: true,
        onSuccess: () => {
            alert('Application submitted! We\'ll be in touch soon.');
            form.reset();
        },
    });
}
</script>
