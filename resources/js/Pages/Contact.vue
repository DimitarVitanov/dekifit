<template>
    <AppLayout title="Contact DekiFit" description="Get in touch with coach Dejan Badzukov for questions about DekiFit bodybuilding programs and nutrition plans.">
        <!-- Hero -->
        <section class="relative pt-32 pb-16 px-6 overflow-hidden">
            <div class="absolute inset-0">
                <Lightfall 
                    :colors="['#f2b90c', '#e8b52c', '#f5f5f5']"
                    background-color="#050505"
                    :speed="0.3"
                    :streak-count="2"
                    :glow="1"
                    :opacity="0.3"
                    class="w-full h-full"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-dark/80 to-dark"></div>
            </div>
            
            <div class="relative z-10 max-w-4xl mx-auto text-center">
                <p class="text-emerald-400 text-xs uppercase tracking-[0.5em] font-semibold mb-6">Get In Touch</p>
                <h1 class="font-display text-5xl md:text-6xl text-off-white mb-6 uppercase">
                    <SplitText 
                        text="Let's Talk" 
                        :delay="50"
                        animation-from="{ opacity: 0, y: 40 }"
                        animation-to="{ opacity: 1, y: 0 }"
                    />
                </h1>
                <p class="text-text-body text-lg max-w-xl mx-auto">
                    Have questions about a package, or want help picking the right one? Reach out directly.
                </p>
            </div>
        </section>

        <!-- Contact Form & Info -->
        <section class="py-16 md:py-24 px-6">
            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16">
                <!-- Form -->
                <div>
                    <h2 class="font-display text-3xl text-off-white mb-8 uppercase">Send a Message</h2>
                    
                    <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-emerald-500/20 border border-emerald-500/30 rounded-xl text-emerald-300 text-sm">
                        {{ $page.props.flash.success }}
                    </div>
                    
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Spam trap: hidden from people, filled in by bots. -->
                        <div class="absolute -left-[9999px] w-px h-px overflow-hidden" aria-hidden="true">
                            <label for="contact_website">Website</label>
                            <input id="contact_website" v-model="form.contact_website" type="text" name="contact_website" tabindex="-1" autocomplete="off">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm text-warm-grey mb-2">Name *</label>
                                <input 
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="w-full px-4 py-3 bg-dark-grey/50 border border-emerald-500/20 rounded-xl text-off-white placeholder-warm-grey focus:border-emerald-500/50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                    placeholder="Your name"
                                >
                                <p v-if="form.errors.name" class="mt-1 text-red-400 text-xs">{{ form.errors.name }}</p>
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
                                <p v-if="form.errors.email" class="mt-1 text-red-400 text-xs">{{ form.errors.email }}</p>
                            </div>
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm text-warm-grey mb-2">Phone (optional)</label>
                            <input 
                                id="phone"
                                v-model="form.phone"
                                type="tel"
                                class="w-full px-4 py-3 bg-dark-grey/50 border border-emerald-500/20 rounded-xl text-off-white placeholder-warm-grey focus:border-emerald-500/50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                placeholder="+1 (555) 000-0000"
                            >
                        </div>
                        
                        <div>
                            <label for="subject" class="block text-sm text-warm-grey mb-2">Subject</label>
                            <select 
                                id="subject"
                                v-model="form.subject"
                                class="w-full px-4 py-3 bg-dark-grey/50 border border-emerald-500/20 rounded-xl text-off-white focus:border-emerald-500/50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all"
                            >
                                <option value="">Select a topic</option>
                                <option value="packages">Packages & Pricing</option>
                                <option value="nutrition">Nutrition Questions</option>
                                <option value="order">Existing Order</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="message" class="block text-sm text-warm-grey mb-2">Message *</label>
                            <textarea 
                                id="message"
                                v-model="form.message"
                                required
                                rows="5"
                                class="w-full px-4 py-3 bg-dark-grey/50 border border-emerald-500/20 rounded-xl text-off-white placeholder-warm-grey focus:border-emerald-500/50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all resize-none"
                                placeholder="Tell us how we can help..."
                            ></textarea>
                            <p v-if="form.errors.message" class="mt-1 text-red-400 text-xs">{{ form.errors.message }}</p>
                        </div>
                        
                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="w-full px-8 py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 text-black text-sm tracking-[0.2em] uppercase font-bold rounded-xl hover:from-emerald-400 hover:to-emerald-500 transition-all duration-300 glow-emerald hover:glow-emerald-strong disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ form.processing ? 'Sending...' : 'Send Message' }}
                        </button>
                    </form>
                </div>
                
                <!-- Contact Info -->
                <div class="lg:pl-8">
                    <h2 class="font-display text-3xl text-off-white mb-8 uppercase">Contact Information</h2>
                    
                    <div class="space-y-8">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500/20 to-emerald-600/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-off-white font-semibold mb-1">Email</h3>
                                <a href="mailto:badzukov.d@hotmail.com" class="text-text-body hover:text-emerald-400 transition-colors">badzukov.d@hotmail.com</a>
                                <p class="text-warm-grey text-sm mt-1">I respond within 24 hours</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500/20 to-emerald-600/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-off-white font-semibold mb-1">Phone / WhatsApp / Viber</h3>
                                <a href="https://wa.me/38971271390" target="_blank" rel="noopener" class="text-text-body hover:text-emerald-400 transition-colors">071 271 390</a>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500/20 to-emerald-600/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-off-white font-semibold mb-1">Social Media</h3>
                                <div class="flex items-center gap-4 mt-2">
                                    <a href="https://www.instagram.com/ddbadzukov3_" target="_blank" rel="noopener" aria-label="Instagram" class="text-warm-grey hover:text-emerald-400 transition-colors">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                    </a>
                                    <a href="https://www.facebook.com/DejanBadzukov" target="_blank" rel="noopener" aria-label="Facebook" class="text-warm-grey hover:text-emerald-400 transition-colors">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.234 2.686.234v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Packages Teaser -->
                    <div class="mt-12 p-6 bg-dark-grey/30 border border-emerald-500/10 rounded-2xl">
                        <h3 class="text-off-white font-semibold mb-3">Ready to Start?</h3>
                        <p class="text-text-body text-sm mb-4">Check out the Basic, Pro, and Elite packages and subscribe via bank transfer.</p>
                        <Link href="/programs" class="text-emerald-400 text-sm font-semibold hover:text-emerald-300 transition-colors flex items-center gap-2">
                            View Packages
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Lightfall from '@/components/vue-bits/Lightfall.vue';
import SplitText from '@/components/vue-bits/SplitText.vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: '',
    contact_website: '',
});

function submit() {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
</script>
