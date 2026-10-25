<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import Icon from '@/components/portal/Icon.vue';
import StatusBadge from '@/components/portal/StatusBadge.vue';
import WeightChart from '@/components/portal/WeightChart.vue';
import EmptyState from '@/components/portal/EmptyState.vue';
import { fmtDate, fmtDay, fmtTime, fromNow, localDateKey } from '@/lib/format';
import { currentWeek, planWeeks } from '@/lib/plan';

const props = defineProps({
    program: { type: Object, default: null },
    stats: { type: Object, required: true },
    weightSeries: { type: Array, default: () => [] },
    nextSession: { type: Object, default: null },
    announcements: { type: Array, default: () => [] },
    tickets: { type: Array, default: () => [] },
    recentNotes: { type: Array, default: () => [] },
    plan: { type: Object, default: null },
    videoCount: { type: Number, default: 0 },
    unlockedVideoCount: { type: Number, default: 0 },
    nutrition: { type: Object, default: null },
});

const planWeek = computed(() => (props.plan ? currentWeek(props.plan, props.stats.member_since) : null));
const planSummary = computed(() => {
    if (!props.plan) return 'Your coach is preparing it';
    if (planWeek.value) return `Week ${planWeek.value} of ${planWeeks(props.plan).length}`;
    return `${props.plan.days.length} training ${props.plan.days.length === 1 ? 'day' : 'days'}`;
});

const firstName = computed(() => usePage().props.auth.user.name.split(' ')[0]);

const greeting = computed(() => {
    const hour = new Date().getHours();
    return hour < 12 ? 'Good morning' : hour < 18 ? 'Good afternoon' : 'Good evening';
});

const loggedToday = computed(() => props.stats.last_logged_on === localDateKey());

const daysIn = computed(() =>
    Math.max(1, Math.floor((Date.now() - new Date(props.stats.member_since).getTime()) / 86400000) + 1)
);

const change = computed(() => {
    const d = props.stats.weight_change;
    if (d === null || d === undefined) return '—';
    return d === 0 ? '±0 kg' : `${d > 0 ? '+' : ''}${d} kg`;
});

const tiles = computed(() => [
    { label: 'Current weight', value: props.stats.current_weight != null ? `${props.stats.current_weight} kg` : '—', hint: props.stats.last_logged_on ? `Logged ${fmtDate(props.stats.last_logged_on)}` : 'No check-in yet', icon: 'trend' },
    { label: 'Change since start', value: change.value, hint: props.stats.start_weight != null ? `Started at ${props.stats.start_weight} kg` : 'Log two check-ins', icon: 'chart' },
    { label: 'Check-ins logged', value: props.stats.check_ins, hint: loggedToday.value ? 'Today is logged' : 'Nothing logged today', icon: 'check' },
    { label: 'Days with DekiFit', value: daysIn.value, hint: `Since ${fmtDate(props.stats.member_since)}`, icon: 'calendar' },
]);
</script>

<template>
    <PortalLayout title="Dashboard">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6">
            <!-- Welcome -->
            <section class="relative xl:col-span-2 overflow-hidden rounded-3xl border border-gold-500/20 bg-gradient-to-br from-gold-900/50 via-dark-grey to-dark-grey p-6 sm:p-8 lg:p-10">
                <div class="pointer-events-none absolute -top-24 -right-24 w-80 h-80 rounded-full bg-gold-500/20 blur-3xl"></div>
                <img src="/images/dekifit-logo.webp" alt="" width="220" height="220" loading="lazy" class="pointer-events-none hidden sm:block absolute -right-6 -bottom-10 w-56 opacity-20 rotate-6">
                <div class="relative">
                    <p v-if="program" class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gold-500 text-black text-[11px] uppercase tracking-[0.2em] font-bold mb-5">
                        {{ program.title }} package
                    </p>
                    <h1 class="font-display text-3xl sm:text-5xl uppercase leading-tight text-off-white">
                        {{ greeting }},<br>
                        <span class="text-gradient">{{ firstName }}</span>
                    </h1>
                    <p class="text-text-body mt-4 max-w-lg">
                        {{ loggedToday ? "Today's check-in is logged. Keep the streak going — it's a marathon, not a sprint." : "It's a marathon, not a sprint. Log today's check-in and keep moving forward." }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 mt-7">
                        <Link href="/portal/progress" class="p-btn">
                            <Icon name="plus" class="w-4 h-4" />
                            {{ loggedToday ? 'Update check-in' : "Log today's check-in" }}
                        </Link>
                        <Link href="/portal/support" class="p-btn-ghost py-3">
                            <Icon name="chat" class="w-4 h-4" />
                            Ask your coach
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Next session -->
            <section class="p-card p-6 sm:p-8 flex flex-col">
                <div class="flex items-center justify-between mb-5">
                    <p class="p-eyebrow">Next session</p>
                    <Icon name="video" class="w-5 h-5 text-gold-400" />
                </div>
                <template v-if="nextSession">
                    <p class="font-display text-2xl uppercase text-off-white leading-snug">{{ fmtDay(nextSession.starts_at) }}</p>
                    <p class="text-gold-300 text-lg font-semibold mt-1 tabular-nums">{{ fmtTime(nextSession.starts_at) }} – {{ fmtTime(nextSession.ends_at) }}</p>
                    <p class="text-warm-grey text-sm mt-1">{{ fromNow(nextSession.starts_at) }}</p>
                    <div class="mt-auto pt-6 flex flex-wrap gap-3">
                        <a v-if="nextSession.meeting_url" :href="nextSession.meeting_url" target="_blank" rel="noopener" class="p-btn">Join call</a>
                        <Link href="/portal/schedule" class="p-btn-ghost">Manage</Link>
                    </div>
                </template>
                <template v-else>
                    <p class="font-display text-2xl uppercase text-off-white leading-snug">Nothing booked</p>
                    <p class="text-text-body text-sm mt-2">Pick a time that works for you and your coach will be there.</p>
                    <div class="mt-auto pt-6">
                        <Link href="/portal/schedule" class="p-btn w-full sm:w-auto xl:w-full">
                            <Icon name="calendar" class="w-4 h-4" />
                            Book a session
                        </Link>
                    </div>
                </template>
            </section>

            <!-- Stat tiles -->
            <section class="xl:col-span-3 grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <div v-for="tile in tiles" :key="tile.label" class="p-card p-4 sm:p-6">
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <p class="text-warm-grey text-xs sm:text-sm">{{ tile.label }}</p>
                        <Icon :name="tile.icon" class="w-4 h-4 sm:w-5 sm:h-5 text-gold-400 flex-shrink-0" />
                    </div>
                    <p class="font-display text-2xl sm:text-4xl text-off-white tabular-nums">{{ tile.value }}</p>
                    <p class="text-warm-grey text-xs mt-2 truncate">{{ tile.hint }}</p>
                </div>
            </section>

            <!-- Training shortcuts -->
            <section class="xl:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                <Link href="/portal/program" class="p-card p-5 sm:p-6 flex items-center gap-4 hover:border-gold-500/30 transition-colors group">
                    <span class="w-12 h-12 rounded-2xl bg-gold-500/10 text-gold-400 flex items-center justify-center flex-shrink-0"><Icon name="dumbbell" class="w-6 h-6" /></span>
                    <span class="min-w-0 flex-1">
                        <span class="p-eyebrow block mb-1">My program</span>
                        <span class="block text-off-white font-semibold truncate group-hover:text-gold-300 transition-colors">{{ plan ? plan.title : 'Your program is on its way' }}</span>
                        <span class="block text-sm text-warm-grey">{{ planSummary }}</span>
                    </span>
                    <Icon name="arrow" class="w-5 h-5 text-warm-grey flex-shrink-0" />
                </Link>
                <Link href="/portal/nutrition" class="relative overflow-hidden p-card p-5 sm:p-6 flex items-center gap-4 hover:border-gold-500/30 transition-colors group">
                    <img src="/images/nutrition-plan.webp" alt="" width="619" height="920" loading="lazy" class="absolute inset-0 w-full h-full object-cover object-[50%_75%] opacity-60 group-hover:opacity-80 transition-opacity">
                    <span class="absolute inset-0 bg-gradient-to-r from-dark-grey via-dark-grey/85 to-dark-grey/10"></span>
                    <span class="relative w-12 h-12 rounded-2xl bg-gold-500/15 text-gold-400 flex items-center justify-center flex-shrink-0"><Icon name="fire" class="w-6 h-6" /></span>
                    <span class="relative min-w-0 flex-1">
                        <span class="p-eyebrow block mb-1">Nutrition plan</span>
                        <span class="block text-off-white font-semibold truncate group-hover:text-gold-300 transition-colors">{{ nutrition ? nutrition.title : 'Your plan is on its way' }}</span>
                        <span class="block text-sm text-text-body">{{ nutrition ? (nutrition.calories ? `${nutrition.calories.toLocaleString()} kcal · meals & recipes` : 'Meals & recipes') : 'Your coach is preparing it' }}</span>
                    </span>
                    <Icon name="arrow" class="relative w-5 h-5 text-off-white/70 flex-shrink-0" />
                </Link>
                <Link href="/portal/videos" class="p-card p-5 sm:p-6 flex items-center gap-4 hover:border-gold-500/30 transition-colors group">
                    <span class="w-12 h-12 rounded-2xl bg-gold-500/10 text-gold-400 flex items-center justify-center flex-shrink-0"><Icon name="play" class="w-6 h-6" /></span>
                    <span class="min-w-0 flex-1">
                        <span class="p-eyebrow block mb-1">Exercise videos</span>
                        <span class="block text-off-white font-semibold truncate group-hover:text-gold-300 transition-colors">Technique library</span>
                        <span class="block text-sm text-warm-grey">{{ !videoCount ? 'Videos coming soon' : unlockedVideoCount < videoCount ? `${unlockedVideoCount} of ${videoCount} unlocked` : `${videoCount} ${videoCount === 1 ? 'video' : 'videos'}` }}</span>
                    </span>
                    <Icon name="arrow" class="w-5 h-5 text-warm-grey flex-shrink-0" />
                </Link>
            </section>

            <!-- Weight trend -->
            <section class="p-card xl:col-span-2 p-4 sm:p-8">
                <div class="flex items-center justify-between gap-4 mb-4 px-2 sm:px-0">
                    <div>
                        <p class="p-eyebrow mb-1">Progress</p>
                        <h2 class="font-display text-xl uppercase text-off-white">Weight trend <span class="text-warm-grey text-sm normal-case font-sans font-normal">(kg)</span></h2>
                    </div>
                    <Link href="/portal/progress" class="text-sm font-semibold text-gold-400 hover:text-gold-300 inline-flex items-center gap-1.5 whitespace-nowrap">
                        All entries <Icon name="arrow" class="w-4 h-4" />
                    </Link>
                </div>
                <WeightChart :points="weightSeries" />
            </section>

            <!-- News -->
            <section class="p-card p-6 sm:p-8">
                <div class="flex items-center justify-between gap-4 mb-5">
                    <div>
                        <p class="p-eyebrow mb-1">From your coach</p>
                        <h2 class="font-display text-xl uppercase text-off-white">News</h2>
                    </div>
                    <Link href="/portal/news" class="text-sm font-semibold text-gold-400 hover:text-gold-300 whitespace-nowrap">View all</Link>
                </div>
                <ul v-if="announcements.length" class="space-y-5">
                    <li v-for="item in announcements" :key="item.id" class="border-l-2 pl-4" :class="item.is_pinned ? 'border-gold-500' : 'border-white/10'">
                        <p class="text-xs text-warm-grey mb-1">{{ item.is_pinned ? 'Pinned · ' : '' }}{{ fromNow(item.published_at) }}</p>
                        <p class="text-off-white font-semibold leading-snug">{{ item.title }}</p>
                        <p class="text-text-body text-sm mt-1 line-clamp-2">{{ item.body }}</p>
                    </li>
                </ul>
                <EmptyState v-else icon="bell" title="No news yet" text="Announcements from your coach will show up here." />
            </section>

            <!-- Questions -->
            <section class="p-card xl:col-span-2 p-6 sm:p-8">
                <div class="flex items-center justify-between gap-4 mb-5">
                    <div>
                        <p class="p-eyebrow mb-1">Support</p>
                        <h2 class="font-display text-xl uppercase text-off-white">Your open questions</h2>
                    </div>
                    <Link href="/portal/support" class="text-sm font-semibold text-gold-400 hover:text-gold-300 whitespace-nowrap">View all</Link>
                </div>
                <ul v-if="tickets.length" class="divide-y divide-white/5">
                    <li v-for="ticket in tickets" :key="ticket.id">
                        <Link :href="`/portal/support/${ticket.id}`" class="flex items-center justify-between gap-4 py-3.5 group">
                            <div class="min-w-0">
                                <p class="text-off-white font-medium truncate group-hover:text-gold-300 transition-colors">{{ ticket.subject }}</p>
                                <p class="text-xs text-warm-grey mt-0.5">Updated {{ fromNow(ticket.last_reply_at) }}</p>
                            </div>
                            <StatusBadge :status="ticket.status" />
                        </Link>
                    </li>
                </ul>
                <EmptyState v-else icon="chat" title="No open questions" text="Stuck on an exercise or a meal? Your coach is one message away.">
                    <Link href="/portal/support" class="p-btn-ghost">Ask a question</Link>
                </EmptyState>
            </section>

            <!-- Notes -->
            <section class="p-card p-6 sm:p-8">
                <div class="flex items-center justify-between gap-4 mb-5">
                    <div>
                        <p class="p-eyebrow mb-1">Private</p>
                        <h2 class="font-display text-xl uppercase text-off-white">Your notes</h2>
                    </div>
                    <Link href="/portal/notes" class="text-sm font-semibold text-gold-400 hover:text-gold-300 whitespace-nowrap">Open</Link>
                </div>
                <ul v-if="recentNotes.length" class="space-y-3">
                    <li v-for="note in recentNotes" :key="note.id">
                        <Link href="/portal/notes" class="flex items-center gap-3 p-3 -mx-3 rounded-xl hover:bg-white/5 transition-colors">
                            <span class="w-9 h-9 rounded-xl bg-gold-500/10 text-gold-400 flex items-center justify-center flex-shrink-0"><Icon name="note" class="w-4 h-4" /></span>
                            <span class="min-w-0">
                                <span class="block text-off-white text-sm font-medium truncate">{{ note.title }}</span>
                                <span class="block text-xs text-warm-grey">{{ fromNow(note.updated_at) }}</span>
                            </span>
                        </Link>
                    </li>
                </ul>
                <EmptyState v-else icon="note" title="No notes yet" text="Keep training cues, PRs and reminders in one place." />
            </section>
        </div>
    </PortalLayout>
</template>
