<script setup>
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({ demoLogins: { type: Array, default: () => [] } });

const flash = computed(() => usePage().props.flash ?? {});

const form = useForm({ email: '', password: '', remember: true });

function submit() {
    form.post('/login', { onFinish: () => form.reset('password') });
}

function useDemo(login) {
    form.email = login.email;
    form.password = login.password;
    submit();
}
</script>

<template>
    <Head title="Client Login">
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
                <h1 class="font-display text-3xl uppercase text-off-white mb-2">Welcome back</h1>
                <p class="text-text-body text-sm mb-8">Log in with the details we emailed you when your payment was confirmed.</p>

                <div v-if="flash.success || flash.error" class="mb-6 px-4 py-3 rounded-xl border text-sm" :class="flash.error ? 'bg-red-500/10 border-red-500/20 text-red-200' : 'bg-green-500/10 border-green-500/20 text-green-200'" role="status">
                    {{ flash.error || flash.success }}
                </div>

                <div class="space-y-5">
                    <div>
                        <label for="email" class="p-label">Email</label>
                        <input id="email" v-model="form.email" type="email" required autofocus autocomplete="username" class="p-input" placeholder="you@email.com">
                        <p v-if="form.errors.email" class="p-error">{{ form.errors.email }}</p>
                    </div>
                    <div>
                        <div class="flex items-baseline justify-between gap-4">
                            <label for="password" class="p-label">Password</label>
                            <Link href="/forgot-password" class="text-xs font-semibold text-gold-400 hover:text-gold-300">Forgot password?</Link>
                        </div>
                        <input id="password" v-model="form.password" type="password" required autocomplete="current-password" class="p-input">
                        <p v-if="form.errors.password" class="p-error">{{ form.errors.password }}</p>
                    </div>
                    <label class="flex items-center gap-3 text-sm text-text-body cursor-pointer">
                        <input v-model="form.remember" type="checkbox" class="w-4 h-4 accent-gold-500">
                        Keep me logged in on this device
                    </label>
                </div>

                <button type="submit" class="p-btn w-full mt-8 py-3.5" :disabled="form.processing">
                    {{ form.processing ? 'Logging in…' : 'Log in' }}
                </button>
            </form>

            <div v-if="demoLogins.length" class="mt-6 rounded-2xl border border-dashed border-gold-500/30 bg-gold-500/5 p-5">
                <p class="p-eyebrow text-gold-400 mb-1">Demo logins</p>
                <p class="text-xs text-warm-grey mb-4">Local preview only — not shown on the live site. Click one to log in.</p>
                <div class="space-y-3">
                    <button v-for="login in demoLogins" :key="login.email" type="button" class="w-full flex items-center justify-between gap-4 rounded-xl border border-white/10 bg-dark/50 px-4 py-3 text-left hover:border-gold-500/40 transition-colors disabled:opacity-50" :disabled="form.processing" @click="useDemo(login)">
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-off-white">{{ login.label }}</span>
                            <span class="block text-xs text-text-body font-mono break-all">{{ login.email }} · {{ login.password }}</span>
                        </span>
                        <span class="text-xs font-semibold text-gold-400 whitespace-nowrap">Log in →</span>
                    </button>
                </div>
            </div>

            <p class="text-center text-sm text-warm-grey mt-6">
                Not a client yet?
                <Link href="/programs" class="text-gold-400 font-semibold hover:text-gold-300">See the packages</Link>
            </p>
        </div>
    </div>
</template>
