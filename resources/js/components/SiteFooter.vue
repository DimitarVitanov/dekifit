<template>
    <footer class="bg-dark-warm border-t border-emerald-500/10">
        <div class="max-w-7xl mx-auto px-6 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div class="md:col-span-1">
                    <Link href="/" class="flex items-center gap-3 group mb-6">
                        <img src="/images/dekifit-logo-96.webp" srcset="/images/dekifit-logo-96.webp 96w, /images/dekifit-logo-192.webp 192w" sizes="40px" alt="DekiFit logo" width="40" height="40" loading="lazy" class="w-10 h-10 rounded-lg object-cover">
                        <span class="font-display text-xl text-off-white tracking-wide">DEKI<span class="text-emerald-400">FIT</span></span>
                    </Link>
                    <p class="text-text-body text-sm leading-relaxed">
                        Train. Focus. Transform. Personalized bodybuilding programs and nutrition plans by coach Dejan Badzukov.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-emerald-400 text-xs uppercase tracking-[0.3em] font-semibold mb-6">Programs</h3>
                    <ul class="space-y-3">
                        <li><Link href="/programs" class="text-text-body text-sm hover:text-emerald-400 transition-colors">Basic Package</Link></li>
                        <li><Link href="/programs" class="text-text-body text-sm hover:text-emerald-400 transition-colors">Pro Package</Link></li>
                        <li><Link href="/programs" class="text-text-body text-sm hover:text-emerald-400 transition-colors">Elite Package</Link></li>
                        <li><Link href="/nutrition" class="text-text-body text-sm hover:text-emerald-400 transition-colors">Nutrition Plans</Link></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-emerald-400 text-xs uppercase tracking-[0.3em] font-semibold mb-6">Company</h3>
                    <ul class="space-y-3">
                        <li><Link href="/about" class="text-text-body text-sm hover:text-emerald-400 transition-colors">About Dejan</Link></li>
                        <li><Link href="/contact" class="text-text-body text-sm hover:text-emerald-400 transition-colors">Contact</Link></li>
                        <li><Link href="/privacy-policy" class="text-text-body text-sm hover:text-emerald-400 transition-colors">Privacy Policy</Link></li>
                        <li><Link href="/terms-of-service" class="text-text-body text-sm hover:text-emerald-400 transition-colors">Terms of Service</Link></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-emerald-400 text-xs uppercase tracking-[0.3em] font-semibold mb-6">Get In Touch</h3>
                    <ul class="space-y-3">
                        <li><a href="mailto:dbadzukov@gmail.com" class="text-text-body text-sm hover:text-emerald-400 transition-colors">dbadzukov@gmail.com</a></li>
                        <li><a href="https://wa.me/38971271390" target="_blank" rel="noopener" class="text-text-body text-sm hover:text-emerald-400 transition-colors">071 271 390 (WhatsApp/Viber)</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-16 pt-8 border-t border-emerald-500/10 flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-warm-grey text-sm">&copy; {{ new Date().getFullYear() }} DekiFit — Dejan Badzukov. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <a href="https://www.instagram.com/ddbadzukov3_" target="_blank" rel="noopener" aria-label="DekiFit on Instagram" class="text-warm-grey hover:text-emerald-400 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <a href="https://www.facebook.com/DejanBadzukov" target="_blank" rel="noopener" aria-label="DekiFit on Facebook" class="text-warm-grey hover:text-emerald-400 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.234 2.686.234v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="https://wa.me/38971271390" target="_blank" rel="noopener" aria-label="DekiFit on WhatsApp" class="text-warm-grey hover:text-emerald-400 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.988.548 1.933.871 3.14.872 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.714-5.759-5.714zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793 0-.853.448-1.273.607-1.446.159-.173.346-.217.462-.217.116 0 .231.001.332.006.106.005.249-.04.39.298.144.347.489 1.2.532 1.287.043.087.072.188.014.304-.058.116-.101.188-.202.304-.101.116-.211.26-.302.348-.101.101-.207.211-.089.42.116.209.518.906 1.114 1.469.766.729 1.44 1.017 1.665 1.126.225.101.36.087.492-.043.144-.144.577-.667.732-.897.144-.23.288-.194.492-.116.202.087 1.28.606 1.499.716.216.109.36.166.418.26.058.093.058.535-.086.94z"/><path d="M12 2C6.477 2 2 6.477 2 12c0 1.936.553 3.744 1.51 5.28L2 22l4.876-1.482A9.953 9.953 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm0 18.15a8.14 8.14 0 01-4.42-1.293l-.317-.202-3.02.918.938-2.972-.207-.328A8.106 8.106 0 013.85 12c0-4.494 3.656-8.15 8.15-8.15 4.494 0 8.15 3.656 8.15 8.15 0 4.494-3.656 8.15-8.15 8.15z"/></svg>
                    </a>
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-warm-grey">
                Powered by <a href="https://nyxeon.io" target="_blank" rel="noopener" class="text-off-white hover:text-emerald-400 transition-colors">nyxeon.io</a>
            </p>
        </div>
    </footer>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
