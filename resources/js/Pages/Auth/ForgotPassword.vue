<script setup>
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const flash = computed(() => usePage().props.flash ?? {});
const form = useForm({ email: '' });

function submit() {
    form.post('/forgot-password', { preserveScroll: true });
}
</script>

<template>
    <Head title="Forgot Password">
        <meta name="robots" content="noindex, nofollow">
        <link rel="icon" type="image/png" href="/favicon-32.png">
    </Head>

    <div class="relative min-h-screen bg-dark flex items-center justify-center px-4 py-12 overflow-hidden">
        <div class="pointer-events-none absolute -top-40 left-1/2 -translate-x-1/2 w-[640px] h-[640px] rounded-full bg-gold-500/10 blur-3xl"></div>

        <div class="page-enter relative w-full max-w-md">
            <Link href="/" class="flex flex-col items-center gap-4 mb-8">
                <img src="/images/dekifit-logo-144.webp" srcset="/images/dekifit-logo-144.webp 144w, /images/dekifit-logo.webp 512w" sizes="72px" alt="DekiFit" width="72" height="72" class="w-18 h-18 rounded-2xl object-cover glow-emerald">
                <span class="font-display text-2xl tracking-wide text-off-white">DEKI<span class="text-gold-400">FIT</span></span>
            </Link>

            <form class="p-card p-6 sm:p-8" @submit.prevent="submit">
                <p class="p-eyebrow text-gold-400 mb-2">Client dashboard</p>
                <h1 class="font-display text-3xl uppercase text-off-white mb-2">Forgot password?</h1>
                <p class="text-text-body text-sm mb-8">Enter the email you log in with and we'll send you a link to choose a new password.</p>

                <div v-if="flash.success || flash.error" class="mb-6 px-4 py-3 rounded-xl border text-sm" :class="flash.error ? 'bg-red-500/10 border-red-500/20 text-red-200' : 'bg-green-500/10 border-green-500/20 text-green-200'" role="status">
                    {{ flash.error || flash.success }}
                </div>

                <div>
                    <label for="email" class="p-label">Email</label>
                    <input id="email" v-model="form.email" type="email" required autofocus autocomplete="username" class="p-input" placeholder="you@email.com">
                    <p v-if="form.errors.email" class="p-error">{{ form.errors.email }}</p>
                </div>

                <button type="submit" class="p-btn w-full mt-8 py-3.5" :disabled="form.processing">
                    {{ form.processing ? 'Sending…' : 'Email me a reset link' }}
                </button>
            </form>

            <p class="text-center text-sm text-warm-grey mt-6">
                Remembered it?
                <Link href="/login" class="text-gold-400 font-semibold hover:text-gold-300">Back to login</Link>
            </p>
        </div>
    </div>
</template>
