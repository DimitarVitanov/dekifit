<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import PageHeader from '@/components/portal/PageHeader.vue';
import EmptyState from '@/components/portal/EmptyState.vue';
import Icon from '@/components/portal/Icon.vue';
import { fmtDate } from '@/lib/format';

const props = defineProps({ plans: { type: Array, default: () => [] } });

const planId = ref(props.plans[0]?.id ?? null);
const plan = computed(() => props.plans.find(item => item.id === planId.value) ?? props.plans[0]);

const targets = computed(() =>
    [
        { label: 'Calories', value: plan.value.calories, unit: 'kcal' },
        { label: 'Protein', value: plan.value.protein_g, unit: 'g' },
        { label: 'Carbs', value: plan.value.carbs_g, unit: 'g' },
        { label: 'Fat', value: plan.value.fat_g, unit: 'g' },
    ].filter(target => target.value != null)
);
</script>

<template>
    <PortalLayout title="Nutrition plan">
        <PageHeader eyebrow="Nutrition" title="Nutrition plan" subtitle="Your daily targets and meals from your coach." />

        <template v-if="plan">
            <div v-if="plans.length > 1" class="flex flex-wrap gap-2 mb-6" role="tablist" aria-label="Plan">
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

            <div v-if="targets.length" class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-4 sm:mb-6">
                <div v-for="(target, i) in targets" :key="target.label" class="p-card p-4 sm:p-6" :class="i === 0 && target.label === 'Calories' ? 'border-gold-500/30' : ''">
                    <p class="text-warm-grey text-xs sm:text-sm mb-2">{{ target.label }} <span class="text-warm-grey/70">/ day</span></p>
                    <p class="font-display text-3xl sm:text-4xl text-off-white tabular-nums">
                        {{ target.value.toLocaleString() }}<span class="font-sans text-sm font-medium text-text-body ml-1.5">{{ target.unit }}</span>
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6">
                <section class="p-card overflow-hidden self-start xl:order-2">
                    <div class="relative h-44 sm:h-56">
                        <img src="/images/nutrition-plan.webp" alt="" width="619" height="920" class="absolute inset-0 w-full h-full object-cover object-[50%_75%]">
                        <span class="absolute inset-0 bg-gradient-to-t from-dark-grey via-transparent to-transparent"></span>
                    </div>
                    <div class="px-6 pb-6 sm:px-8 sm:pb-8 -mt-6 relative">
                        <p class="p-eyebrow text-gold-400 mb-2">{{ plan.user_id ? 'Made for you' : 'Your package' }}</p>
                        <h2 class="font-display text-2xl uppercase text-off-white leading-snug break-words">{{ plan.title }}</h2>
                        <p v-if="plan.description" class="text-text-body text-sm mt-3 leading-relaxed whitespace-pre-wrap break-words">{{ plan.description }}</p>
                        <p class="text-xs text-warm-grey mt-4">Updated {{ fmtDate(plan.updated_at) }}</p>
                        <Link href="/portal/support" class="p-btn-ghost w-full mt-5">
                            <Icon name="chat" class="w-4 h-4" /> Question about your plan?
                        </Link>
                    </div>
                </section>

                <ol class="xl:col-span-2 xl:order-1 space-y-3 sm:space-y-4 min-w-0">
                    <li v-for="(meal, i) in plan.meals" :key="i" class="p-card p-5 sm:p-6">
                        <div class="flex items-center gap-4 mb-4">
                            <span class="w-10 h-10 rounded-xl bg-gold-500/10 text-gold-400 font-display text-lg flex items-center justify-center flex-shrink-0">{{ i + 1 }}</span>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-display text-xl uppercase text-off-white leading-snug break-words">{{ meal.name }}</h3>
                                <p v-if="meal.time" class="text-sm text-gold-300 tabular-nums">{{ meal.time }}</p>
                            </div>
                        </div>
                        <ul v-if="meal.items?.length" class="divide-y divide-white/5">
                            <li v-for="(item, n) in meal.items" :key="n" class="flex items-baseline justify-between gap-4 py-2.5 text-sm">
                                <span class="text-off-white break-words min-w-0">{{ item.food }}</span>
                                <span v-if="item.amount" class="text-text-body tabular-nums text-right flex-shrink-0 max-w-[45%]">{{ item.amount }}</span>
                            </li>
                        </ul>
                        <p v-if="meal.note" class="text-sm text-text-body mt-3 pl-3 border-l-2 border-gold-500/40 break-words">{{ meal.note }}</p>
                    </li>
                </ol>
            </div>
        </template>
        <div v-else class="p-card">
            <EmptyState icon="fire" title="Your nutrition plan is on its way" text="Your coach is putting your plan together. You'll see your targets and meals here as soon as it's ready.">
                <Link href="/portal/support" class="p-btn-ghost">Message your coach</Link>
            </EmptyState>
        </div>
    </PortalLayout>
</template>
