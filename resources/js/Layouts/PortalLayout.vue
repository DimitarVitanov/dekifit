<script setup>
import { computed, ref, watch } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import Icon from '@/components/portal/Icon.vue';
import { initials } from '@/lib/format';

defineProps({ title: { type: String, required: true } });

const page = usePage();
const user = computed(() => page.props.auth?.user ?? {});
const flash = computed(() => page.props.flash ?? {});
const counts = computed(() => page.props.adminCounts ?? {});

// `primary` items fill the mobile bottom bar; the rest live under "More".
const clientNav = [
    { href: '/portal', label: 'Dashboard', short: 'Home', icon: 'home', primary: true, exact: true },
    { href: '/portal/program', label: 'My program', short: 'Program', icon: 'dumbbell', primary: true },
    { href: '/portal/nutrition', label: 'Nutrition plan', short: 'Nutrition', icon: 'fire', primary: true },
    { href: '/portal/progress', label: 'Progress', icon: 'trend', primary: true },
    { href: '/portal/schedule', label: 'Schedule', icon: 'calendar' },
    { href: '/portal/videos', label: 'Exercise videos', icon: 'video' },
    { href: '/portal/support', label: 'Ask your coach', icon: 'chat' },
    { href: '/portal/news', label: 'News', icon: 'bell' },
    { href: '/portal/notes', label: 'Notes', icon: 'note' },
    { href: '/portal/account', label: 'Account', icon: 'user' },
];

const adminNav = [
    { href: '/admin', label: 'Orders', icon: 'orders', primary: true, exact: true, badge: 'orders' },
    { href: '/admin/clients', label: 'Clients', icon: 'users', primary: true },
    { href: '/admin/tickets', label: 'Tickets', icon: 'chat', primary: true, badge: 'tickets' },
    { href: '/admin/slots', label: 'Time slots', short: 'Slots', icon: 'calendar', primary: true },
    { href: '/admin/programs', label: 'Training programs', icon: 'dumbbell' },
    { href: '/admin/nutrition', label: 'Nutrition plans', icon: 'fire' },
    { href: '/admin/videos', label: 'Exercise videos', icon: 'video' },
    { href: '/admin/news', label: 'News', icon: 'bell' },
];

const nav = computed(() => (user.value.is_admin ? adminNav : clientNav));
const primaryNav = computed(() => nav.value.filter(item => item.primary));
const moreNav = computed(() => nav.value.filter(item => !item.primary));

const path = computed(() => page.url.split('?')[0]);
const isActive = item => (item.exact ? path.value === item.href : path.value.startsWith(item.href));
const moreActive = computed(() => moreNav.value.some(isActive));

const moreOpen = ref(false);
const dismissed = ref(false);
watch(() => page.url, () => { moreOpen.value = false; });
watch(flash, () => { dismissed.value = false; });

const logout = () => router.post('/logout');

const resending = ref(false);
const resendVerification = () => router.post('/email/verification-notification', {}, {
    preserveScroll: true,
    onStart: () => { resending.value = true; },
    onFinish: () => { resending.value = false; },
});
</script>

<template>
    <Head :title="title">
        <meta name="robots" content="noindex, nofollow">
        <meta name="theme-color" content="#050505">
        <link rel="icon" type="image/png" href="/favicon-32.png">
    </Head>

    <div class="min-h-screen bg-dark text-off-white lg:pl-64">
        <!-- Desktop sidebar -->
        <aside class="hidden lg:flex fixed inset-y-0 left-0 w-64 flex-col bg-dark-warm border-r border-white/5">
            <Link href="/" class="flex items-center gap-3 px-6 h-20">
                <img src="/images/dekifit-logo-96.webp" srcset="/images/dekifit-logo-96.webp 96w, /images/dekifit-logo-192.webp 192w" sizes="40px" alt="" width="40" height="40" class="w-10 h-10 rounded-xl object-cover">
                <span class="font-display text-xl tracking-wide">DEKI<span class="text-gold-400">FIT</span></span>
            </Link>

            <nav class="flex-1 overflow-y-auto px-3 py-2 space-y-1" :aria-label="user.is_admin ? 'Admin' : 'Portal'">
                <p class="p-eyebrow px-3 pt-2 pb-3">{{ user.is_admin ? 'Coach admin' : 'Your coaching' }}</p>
                <Link
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors"
                    :class="isActive(item) ? 'bg-gold-500/10 text-gold-300' : 'text-text-body hover:text-off-white hover:bg-white/5'"
                    :aria-current="isActive(item) ? 'page' : undefined"
                >
                    <Icon :name="item.icon" class="w-5 h-5 flex-shrink-0" />
                    <span class="flex-1">{{ item.label }}</span>
                    <span v-if="item.badge && counts[item.badge]" class="min-w-5 h-5 px-1.5 rounded-full bg-gold-500 text-black text-[11px] font-bold flex items-center justify-center">{{ counts[item.badge] }}</span>
                </Link>
            </nav>

            <div class="p-3 border-t border-white/5">
                <div class="flex items-center gap-3 px-3 py-2">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gold-400 to-gold-600 text-black text-xs font-bold flex items-center justify-center flex-shrink-0">{{ initials(user.name) }}</div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold truncate">{{ user.name }}</p>
                        <p class="text-xs text-warm-grey truncate">{{ user.email }}</p>
                    </div>
                </div>
                <button type="button" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-text-body hover:text-off-white hover:bg-white/5 transition-colors" @click="logout">
                    <Icon name="logout" class="w-5 h-5" />
                    Log out
                </button>
            </div>
        </aside>

        <!-- Mobile top bar -->
        <header class="lg:hidden sticky top-0 z-30 flex items-center justify-between gap-3 px-4 h-14 bg-dark/90 backdrop-blur-xl border-b border-white/5">
            <Link href="/" class="flex items-center gap-2.5">
                <img src="/images/dekifit-logo-96.webp" srcset="/images/dekifit-logo-96.webp 96w, /images/dekifit-logo-192.webp 192w" sizes="32px" alt="" width="32" height="32" class="w-8 h-8 rounded-lg object-cover">
                <span class="font-display text-lg tracking-wide">DEKI<span class="text-gold-400">FIT</span></span>
            </Link>
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gold-400 to-gold-600 text-black text-[11px] font-bold flex items-center justify-center">{{ initials(user.name) }}</div>
        </header>

        <main class="page-enter mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-10 pt-6 lg:pt-10 pb-28 lg:pb-12">
            <div v-if="!dismissed && (flash.success || flash.error)" class="mb-6 flex items-start gap-3 px-4 py-3 rounded-xl border text-sm" :class="flash.error ? 'bg-red-500/10 border-red-500/20 text-red-200' : 'bg-green-500/10 border-green-500/20 text-green-200'" role="status">
                <Icon :name="flash.error ? 'close' : 'check'" class="w-5 h-5 flex-shrink-0 mt-px" />
                <p class="flex-1">{{ flash.error || flash.success }}</p>
                <button type="button" class="opacity-70 hover:opacity-100" aria-label="Dismiss" @click="dismissed = true">
                    <Icon name="close" class="w-4 h-4" />
                </button>
            </div>

            <Link v-if="user.must_change_password && path !== '/portal/account'" href="/portal/account" class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl border border-gold-500/20 bg-gold-500/10 text-sm text-gold-100 hover:border-gold-500/40 transition-colors">
                <Icon name="user" class="w-5 h-5 flex-shrink-0 text-gold-400" />
                <span class="flex-1">You're still using the password we emailed you. Set your own to keep your account safe.</span>
                <Icon name="arrow" class="w-4 h-4 flex-shrink-0" />
            </Link>

            <div v-if="!user.is_admin && user.email_verified === false" class="mb-6 flex flex-col sm:flex-row sm:items-center gap-3 px-4 py-3 rounded-xl border border-gold-500/20 bg-gold-500/10 text-sm text-gold-100">
                <div class="flex items-start gap-3 flex-1 min-w-0">
                    <Icon name="bell" class="w-5 h-5 flex-shrink-0 mt-px text-gold-400" />
                    <span>Please confirm your email address (<span class="break-all">{{ user.email }}</span>) so password resets and messages from your coach reach you. Use the link we emailed you.</span>
                </div>
                <button type="button" class="self-start sm:self-auto flex-shrink-0 px-3 py-1.5 rounded-lg border border-gold-500/40 text-xs font-semibold uppercase tracking-wider text-gold-300 hover:bg-gold-500/10 transition-colors disabled:opacity-50" :disabled="resending" @click="resendVerification">
                    {{ resending ? 'Sending…' : 'Resend link' }}
                </button>
            </div>

            <slot />
        </main>

        <!-- Mobile bottom bar -->
        <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-dark-warm/95 backdrop-blur-xl border-t border-white/5 pb-[env(safe-area-inset-bottom)]" aria-label="Main">
            <div class="grid grid-cols-5">
                <Link
                    v-for="item in primaryNav"
                    :key="item.href"
                    :href="item.href"
                    class="relative flex flex-col items-center justify-center gap-1 h-16 text-[11px] font-medium transition-colors"
                    :class="isActive(item) ? 'text-gold-400' : 'text-warm-grey'"
                    :aria-current="isActive(item) ? 'page' : undefined"
                >
                    <Icon :name="item.icon" class="w-6 h-6" />
                    {{ item.short || item.label }}
                    <span v-if="item.badge && counts[item.badge]" class="absolute top-2 left-1/2 ml-2 min-w-4 h-4 px-1 rounded-full bg-gold-500 text-black text-[10px] font-bold flex items-center justify-center">{{ counts[item.badge] }}</span>
                </Link>
                <button type="button" class="flex flex-col items-center justify-center gap-1 h-16 text-[11px] font-medium transition-colors" :class="moreOpen || moreActive ? 'text-gold-400' : 'text-warm-grey'" :aria-expanded="moreOpen" @click="moreOpen = !moreOpen">
                    <Icon name="more" class="w-6 h-6" />
                    More
                </button>
            </div>
        </nav>

        <!-- Mobile "More" sheet -->
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" leave-active-class="transition duration-150 ease-in" leave-to-class="opacity-0">
            <div v-if="moreOpen" class="lg:hidden fixed inset-0 z-30 bg-black/60" @click="moreOpen = false"></div>
        </Transition>
        <Transition enter-active-class="transition duration-250 ease-out" enter-from-class="translate-y-full" leave-active-class="transition duration-200 ease-in" leave-to-class="translate-y-full">
            <div v-if="moreOpen" class="lg:hidden fixed inset-x-0 bottom-0 z-30 bg-dark-grey border-t border-white/10 rounded-t-3xl px-4 pt-5 pb-[calc(5rem+env(safe-area-inset-bottom))]">
                <div class="flex items-center gap-3 px-2 pb-4 mb-2 border-b border-white/5">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gold-400 to-gold-600 text-black text-xs font-bold flex items-center justify-center">{{ initials(user.name) }}</div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold truncate">{{ user.name }}</p>
                        <p class="text-xs text-warm-grey truncate">{{ user.email }}</p>
                    </div>
                </div>
                <Link v-for="item in moreNav" :key="item.href" :href="item.href" class="flex items-center gap-3 px-2 py-3.5 rounded-xl text-sm font-medium" :class="isActive(item) ? 'text-gold-300' : 'text-off-white'">
                    <Icon :name="item.icon" class="w-5 h-5" />
                    {{ item.label }}
                </Link>
                <button type="button" class="w-full flex items-center gap-3 px-2 py-3.5 rounded-xl text-sm font-medium text-text-body" @click="logout">
                    <Icon name="logout" class="w-5 h-5" />
                    Log out
                </button>
            </div>
        </Transition>
    </div>
</template>
