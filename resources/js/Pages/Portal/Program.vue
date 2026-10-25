<script setup>
import { computed, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import PageHeader from '@/components/portal/PageHeader.vue';
import EmptyState from '@/components/portal/EmptyState.vue';
import VideoPlayer from '@/components/portal/VideoPlayer.vue';
import Icon from '@/components/portal/Icon.vue';
import ProgramCalendar from '@/components/portal/ProgramCalendar.vue';
import { fmtDate, fmtDay } from '@/lib/format';
import { currentWeek, dayDate, planWeeks, todayIndex } from '@/lib/plan';

const props = defineProps({
    plans: { type: Array, default: () => [] },
    videos: { type: Object, default: () => ({}) }, // keyed by id
    memberSince: { type: String, default: null },
});

const planId = ref(props.plans[0]?.id ?? null);
const dayIndex = ref(0); // position in plan.days
const watching = ref(null); // index of the exercise whose video is open

const plan = computed(() => props.plans.find(item => item.id === planId.value) ?? props.plans[0]);
const weeks = computed(() => (plan.value ? planWeeks(plan.value) : []));
const thisWeek = computed(() => (plan.value ? currentWeek(plan.value, props.memberSince) : null));
const day = computed(() => plan.value?.days[dayIndex.value] ?? plan.value?.days[0]);
const date = computed(() => (weeks.value.length ? dayDate(plan.value, props.memberSince, dayIndex.value) : null));

// A weekly plan opens on today's day; other plans on their first day.
watch(planId, () => { dayIndex.value = plan.value ? todayIndex(plan.value, props.memberSince) : 0; }, { immediate: true });
watch([planId, dayIndex], () => { watching.value = null; });

const details = exercise =>
    [
        exercise.sets && exercise.reps ? `${exercise.sets} × ${exercise.reps}` : exercise.sets ? `${exercise.sets} sets` : exercise.reps,
        exercise.rest && `Rest ${exercise.rest}`,
    ].filter(Boolean);
</script>

<template>
    <PortalLayout title="My program">
        <PageHeader eyebrow="Training" title="My program" subtitle="Your training plan from your coach. Tap Watch on an exercise to see how it's done." />

        <template v-if="plan">
            <div v-if="plans.length > 1" class="flex flex-wrap gap-2 mb-6" role="tablist" aria-label="Program">
                <button
                    v-for="item in plans"
                    :key="item.id"
                    type="button"
                    role="tab"
                    :aria-selected="item.id === plan.id"
                    class="px-4 py-2 rounded-full border text-sm font-semibold transition-colors"
                    :class="item.id === plan.id ? 'bg-gold-500 border-gold-500 text-black' : 'border-white/10 text-off-white hover:border-gold-500/40'"
                    @click="planId = item.id"
                >
                    {{ item.title }}
                </button>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6">
                <section class="xl:col-span-2 min-w-0">
                    <template v-if="weeks.length">
                        <ProgramCalendar v-model="dayIndex" :plan="plan" :member-since="memberSince" />
                        <div class="flex items-baseline justify-between gap-3 mt-6 mb-3 px-1">
                            <h3 class="font-display text-xl uppercase text-off-white leading-snug break-words min-w-0">{{ day.name }}</h3>
                            <p class="text-sm text-warm-grey flex-shrink-0">{{ fmtDay(date) }}</p>
                        </div>
                    </template>
                    <div v-else class="flex flex-wrap gap-2 pb-3" role="tablist" aria-label="Training day">
                        <button
                            v-for="(item, i) in plan.days"
                            :key="i"
                            type="button"
                            role="tab"
                            :aria-selected="i === dayIndex"
                            class="px-4 py-2.5 rounded-xl border text-sm font-semibold transition-colors"
                            :class="i === dayIndex ? 'bg-gold-500/15 border-gold-500 text-gold-200' : 'border-white/10 text-off-white hover:border-gold-500/40'"
                            @click="dayIndex = i"
                        >
                            {{ item.name }}
                        </button>
                    </div>

                    <ol v-if="day.exercises.length" class="space-y-3 mt-1">
                        <li v-for="(exercise, i) in day.exercises" :key="i" class="p-card p-4 sm:p-5">
                            <div class="flex items-start gap-4">
                                <span class="w-9 h-9 rounded-xl bg-gold-500/10 text-gold-400 font-display text-lg flex items-center justify-center flex-shrink-0">{{ i + 1 }}</span>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-off-white font-semibold leading-snug break-words">{{ exercise.name }}</h3>
                                    <p v-if="details(exercise).length" class="flex flex-wrap gap-x-4 gap-y-1 text-sm mt-1">
                                        <span v-for="(detail, d) in details(exercise)" :key="d" class="tabular-nums" :class="d === 0 ? 'text-gold-300 font-semibold' : 'text-text-body'">{{ detail }}</span>
                                    </p>
                                    <p v-if="exercise.note" class="text-text-body text-sm mt-2 break-words">{{ exercise.note }}</p>
                                </div>
                                <button v-if="videos[exercise.video_id]" type="button" class="p-btn-ghost flex-shrink-0 px-3" :aria-expanded="watching === i" @click="watching = watching === i ? null : i">
                                    <Icon :name="watching === i ? 'close' : 'play'" class="w-4 h-4" />
                                    <span class="hidden sm:inline">{{ watching === i ? 'Close' : 'Watch' }}</span>
                                </button>
                            </div>
                            <VideoPlayer v-if="watching === i" :url="videos[exercise.video_id].url" :title="exercise.name" :poster="videos[exercise.video_id].poster" autoplay class="mt-4" />
                        </li>
                    </ol>
                    <div v-else class="p-card mt-1">
                        <EmptyState icon="dumbbell" title="Rest day" text="No exercises on this day. Recover well." />
                    </div>
                </section>

                <section class="p-card p-6 sm:p-8 self-start">
                    <p class="p-eyebrow text-gold-400 mb-2">{{ plan.user_id ? 'Made for you' : 'Your package' }}</p>
                    <h2 class="font-display text-2xl uppercase text-off-white leading-snug break-words">{{ plan.title }}</h2>
                    <p v-if="thisWeek" class="text-gold-300 text-sm font-semibold mt-1">You're in week {{ thisWeek }} of {{ weeks[weeks.length - 1] }}</p>
                    <p v-if="plan.description" class="text-text-body text-sm mt-3 leading-relaxed whitespace-pre-wrap break-words">{{ plan.description }}</p>
                    <p class="text-xs text-warm-grey mt-4">Updated {{ fmtDate(plan.updated_at) }}</p>
                    <Link href="/portal/support" class="p-btn-ghost w-full mt-5">
                        <Icon name="chat" class="w-4 h-4" /> Question about your program?
                    </Link>
                </section>
            </div>
        </template>
        <div v-else class="p-card">
            <EmptyState icon="dumbbell" title="Your program is on its way" text="Your coach is putting your training plan together. You'll see it here as soon as it's ready.">
                <Link href="/portal/support" class="p-btn-ghost">Message your coach</Link>
            </EmptyState>
        </div>
    </PortalLayout>
</template>
