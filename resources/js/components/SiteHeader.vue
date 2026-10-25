<template>
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-500" :class="scrolled ? 'bg-dark/95 backdrop-blur-xl border-b border-emerald-500/10' : ''">
        <nav class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
            <Link href="/" class="flex items-center gap-3 group">
                <img src="/images/dekifit-logo-96.webp" srcset="/images/dekifit-logo-96.webp 96w, /images/dekifit-logo-192.webp 192w" sizes="44px" alt="DekiFit logo" width="44" height="44" class="w-11 h-11 rounded-lg object-cover glow-emerald group-hover:glow-emerald-strong transition-all duration-300" fetchpriority="high">
                <span class="font-display text-xl text-off-white tracking-wide group-hover:text-emerald-400 transition-colors">DEKI<span class="text-emerald-400">FIT</span></span>
            </Link>
            
            <div class="hidden md:flex items-center gap-8">
                <Link 
                    v-for="item in navItems" 
                    :key="item.href"
                    :href="item.href" 
                    class="text-sm text-warm-grey hover:text-emerald-400 transition-colors duration-300 tracking-wide uppercase"
                    :class="{ 'text-emerald-400': isActive(item.href) }"
                >
                    {{ item.label }}
                </Link>
            </div>
            
            <div class="flex items-center gap-4">
                <Link :href="portalHref" class="hidden sm:inline-flex text-xs text-warm-grey hover:text-emerald-400 transition-colors tracking-[0.15em] uppercase font-semibold whitespace-nowrap">
                    {{ portalLabel }}
                </Link>
                <Link href="/programs" class="hidden sm:inline-flex px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-black text-xs tracking-[0.2em] uppercase font-bold rounded-lg hover:from-emerald-400 hover:to-emerald-500 transition-all duration-300 glow-emerald hover:glow-emerald-strong">
                    Get Started
                </Link>
                <button @click="mobileOpen = !mobileOpen" class="md:hidden text-off-white p-2" :aria-label="mobileOpen ? 'Close menu' : 'Open menu'" :aria-expanded="mobileOpen">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </nav>
        
        <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 -translate-y-4"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-4"
        >
            <div v-if="mobileOpen" class="md:hidden bg-dark-warm/95 backdrop-blur-xl border-b border-emerald-500/10 px-6 py-6">
                <div class="flex flex-col gap-4">
                    <Link 
                        v-for="item in navItems" 
                        :key="item.href"
                        :href="item.href" 
                        class="text-sm text-warm-grey hover:text-emerald-400 transition-colors duration-300 tracking-wide uppercase py-2"
                        :class="{ 'text-emerald-400': isActive(item.href) }"
                        @click="mobileOpen = false"
                    >
                        {{ item.label }}
                    </Link>
                    <Link :href="portalHref" class="text-sm text-warm-grey hover:text-emerald-400 transition-colors duration-300 tracking-wide uppercase py-2" @click="mobileOpen = false">
                        {{ portalLabel }}
                    </Link>
                    <Link href="/programs" class="mt-4 px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-black text-xs tracking-[0.2em] uppercase font-bold rounded-lg text-center" @click="mobileOpen = false">
                        Get Started
                    </Link>
                </div>
            </div>
        </Transition>
    </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const navItems = [
    { href: '/', label: 'Home' },
    { href: '/about', label: 'About' },
    { href: '/programs', label: 'Programs' },
    { href: '/nutrition', label: 'Nutrition' },
    { href: '/contact', label: 'Contact' },
];

// Logged-in clients (or the coach) get a shortcut back to their dashboard.
const authUser = computed(() => usePage().props.auth?.user);
const portalHref = computed(() => (authUser.value ? (authUser.value.is_admin ? '/admin' : '/portal') : '/login'));
const portalLabel = computed(() => (authUser.value ? 'My Dashboard' : 'Client Login'));

const scrolled = ref(false);
const mobileOpen = ref(false);

function handleScroll() {
    scrolled.value = window.scrollY > 50;
}

function isActive(href) {
    const page = usePage();
    return page.url === href;
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>
