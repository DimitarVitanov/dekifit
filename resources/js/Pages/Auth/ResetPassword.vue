<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    token: { type: String, required: true },
    email: { type: String, default: '' },
});

const form = useForm({ token: props.token, email: props.email, password: '', password_confirmation: '' });

function submit() {
    form.post('/reset-password', { onFinish: () => form.reset('password', 'password_confirmation') });
}
</script>

<template>
    <Head title="Choose a New Password">
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
                <h1 class="font-display text-3xl uppercase text-off-white mb-2">New password</h1>
                <p class="text-text-body text-sm mb-8">Choose a password with at least 8 characters.</p>

                <div class="space-y-5">
                    <div>
                        <label for="email" class="p-label">Email</label>
                        <input id="email" v-model="form.email" type="email" required autocomplete="username" class="p-input">
                        <p v-if="form.errors.email" class="p-error">
                            {{ form.errors.email }}
                            <Link href="/forgot-password" class="underline hover:text-off-white">Send a new link</Link>
                        </p>
                    </div>
                    <div>
                        <label for="password" class="p-label">New password</label>
                        <input id="password" v-model="form.password" type="password" required minlength="8" autofocus autocomplete="new-password" class="p-input">
                        <p v-if="form.errors.password" class="p-error">{{ form.errors.password }}</p>
                    </div>
                    <div>
                        <label for="password_confirmation" class="p-label">Repeat new password</label>
                        <input id="password_confirmation" v-model="form.password_confirmation" type="password" required minlength="8" autocomplete="new-password" class="p-input">
                    </div>
                </div>

                <button type="submit" class="p-btn w-full mt-8 py-3.5" :disabled="form.processing">
                    {{ form.processing ? 'Saving…' : 'Save new password' }}
                </button>
            </form>

            <p class="text-center text-sm text-warm-grey mt-6">
                <Link href="/login" class="text-gold-400 font-semibold hover:text-gold-300">Back to login</Link>
            </p>
        </div>
    </div>
</template>
