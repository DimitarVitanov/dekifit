<script setup>
import { nextTick, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import PageHeader from '@/components/portal/PageHeader.vue';
import EmptyState from '@/components/portal/EmptyState.vue';
import Icon from '@/components/portal/Icon.vue';
import { fmtDate } from '@/lib/format';

defineProps({
    plans: { type: Array, default: () => [] },
    clients: { type: Array, default: () => [] },
    packages: { type: Array, default: () => [] },
});

const blankItem = () => ({ food: '', amount: '' });
const blankMeal = n => ({ name: `Meal ${n}`, time: '', note: '', items: [blankItem()] });
const blank = () => ({ title: '', description: '', audience: 'all', calories: '', protein_g: '', carbs_g: '', fat_g: '', meals: [blankMeal(1)] });

const editing = ref(null); // null = closed, 'new' or a plan id
const editor = ref(null);
// audience: 'all', 'package:ID' or 'client:ID'
const form = useForm(blank());

function open(plan = null) {
    editing.value = plan ? plan.id : 'new';
    form.clearErrors();
    Object.assign(form, plan
        ? {
            title: plan.title,
            description: plan.description ?? '',
            audience: plan.user_id ? `client:${plan.user_id}` : plan.program_id ? `package:${plan.program_id}` : 'all',
            calories: plan.calories ?? '',
            protein_g: plan.protein_g ?? '',
            carbs_g: plan.carbs_g ?? '',
            fat_g: plan.fat_g ?? '',
            meals: plan.meals.map(meal => ({ ...blankMeal(1), ...meal, items: (meal.items ?? []).map(item => ({ ...blankItem(), ...item })) })),
        }
        : blank());
    nextTick(() => editor.value?.scrollIntoView({ behavior: 'smooth', block: 'start' }));
}

function duplicate(plan) {
    open(plan);
    editing.value = 'new';
    form.title = `${plan.title} (copy)`;
}

function close() {
    editing.value = null;
    Object.assign(form, blank());
}

function move(list, index, by) {
    const target = index + by;
    if (target < 0 || target >= list.length) return;
    list.splice(target, 0, list.splice(index, 1)[0]);
}

const number = value => (value === '' || value === null ? null : Number(value));

function save() {
    const [kind, id] = form.audience.split(':');
    form.transform(data => ({
        title: data.title,
        description: data.description || null,
        user_id: kind === 'client' ? Number(id) : null,
        program_id: kind === 'package' ? Number(id) : null,
        calories: number(data.calories),
        protein_g: number(data.protein_g),
        carbs_g: number(data.carbs_g),
        fat_g: number(data.fat_g),
        meals: data.meals,
    }));
    const options = { preserveScroll: true, onSuccess: close };
    if (editing.value === 'new') form.post('/admin/nutrition', options);
    else form.put(`/admin/nutrition/${editing.value}`, options);
}

function remove(plan) {
    if (confirm(`Delete "${plan.title}"? Clients will no longer see it.`)) {
        router.delete(`/admin/nutrition/${plan.id}`, { preserveScroll: true, onSuccess: () => editing.value === plan.id && close() });
    }
}

const audienceLabel = plan => (plan.user ? plan.user.name : plan.program ? `${plan.program.title} package` : 'All clients');
const hasErrors = () => Object.keys(form.errors).length > 0;
</script>

<template>
    <PortalLayout title="Nutrition plans">
        <PageHeader eyebrow="Coach admin" title="Nutrition plans" subtitle="Set calories, macros and meals, then give the plan to one client, everyone on a package, or all clients.">
            <button v-if="!editing" type="button" class="p-btn w-full sm:w-auto" @click="open()">
                <Icon name="plus" class="w-4 h-4" /> New plan
            </button>
        </PageHeader>

        <form v-if="editing" ref="editor" class="p-card border-gold-500/30 p-4 sm:p-8 mb-8 scroll-mt-20" @submit.prevent="save">
            <h2 class="font-display text-xl uppercase text-off-white mb-5">{{ editing === 'new' ? 'New nutrition plan' : 'Edit nutrition plan' }}</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="col-span-2">
                    <label for="np-title" class="p-label">Plan name</label>
                    <input id="np-title" v-model="form.title" type="text" required maxlength="255" class="p-input" placeholder="e.g. Fat loss — 2,000 kcal">
                    <p v-if="form.errors.title" class="p-error">{{ form.errors.title }}</p>
                </div>
                <div class="col-span-2">
                    <label for="np-audience" class="p-label">Who is it for?</label>
                    <select id="np-audience" v-model="form.audience" class="p-input">
                        <option value="all">All clients</option>
                        <optgroup label="Everyone on a package">
                            <option v-for="item in packages" :key="item.id" :value="`package:${item.id}`">{{ item.title }} package</option>
                        </optgroup>
                        <optgroup v-if="clients.length" label="One client">
                            <option v-for="item in clients" :key="item.id" :value="`client:${item.id}`">{{ item.name }}</option>
                        </optgroup>
                    </select>
                </div>
                <div>
                    <label for="np-calories" class="p-label">Calories (kcal)</label>
                    <input id="np-calories" v-model="form.calories" type="number" inputmode="numeric" min="500" max="10000" class="p-input" placeholder="2600">
                    <p v-if="form.errors.calories" class="p-error">{{ form.errors.calories }}</p>
                </div>
                <div>
                    <label for="np-protein" class="p-label">Protein (g)</label>
                    <input id="np-protein" v-model="form.protein_g" type="number" inputmode="numeric" min="0" max="1000" class="p-input" placeholder="180">
                </div>
                <div>
                    <label for="np-carbs" class="p-label">Carbs (g)</label>
                    <input id="np-carbs" v-model="form.carbs_g" type="number" inputmode="numeric" min="0" max="2000" class="p-input" placeholder="300">
                </div>
                <div>
                    <label for="np-fat" class="p-label">Fat (g)</label>
                    <input id="np-fat" v-model="form.fat_g" type="number" inputmode="numeric" min="0" max="1000" class="p-input" placeholder="75">
                </div>
                <div class="col-span-2 md:col-span-4">
                    <label for="np-description" class="p-label">Guidelines for the client (optional)</label>
                    <textarea id="np-description" v-model="form.description" rows="3" maxlength="5000" class="p-input" placeholder="Water, free meals, swaps, supplements…"></textarea>
                </div>
            </div>

            <div class="space-y-5 mt-6">
                <fieldset v-for="(meal, m) in form.meals" :key="m" class="rounded-2xl border border-white/10 p-3 sm:p-5 min-w-0">
                    <div class="flex items-center gap-2 mb-3">
                        <input v-model="meal.name" type="text" required maxlength="100" class="p-input font-semibold min-w-0" placeholder="e.g. Breakfast" :aria-label="`Meal ${m + 1} name`">
                        <input v-model="meal.time" type="text" maxlength="30" class="p-input w-24 sm:w-32 px-3 flex-shrink-0" placeholder="Time" aria-label="Time">
                    </div>

                    <div class="space-y-2">
                        <div v-for="(item, n) in meal.items" :key="n" class="flex items-center gap-2">
                            <input v-model="item.food" type="text" required maxlength="150" class="p-input py-2.5 min-w-0 flex-1" placeholder="Food" :aria-label="`Food ${n + 1}`">
                            <input v-model="item.amount" type="text" maxlength="60" class="p-input py-2.5 w-24 sm:w-40 px-3 flex-shrink-0" placeholder="Amount" aria-label="Amount">
                            <button type="button" class="p-2 text-warm-grey hover:text-red-400 flex-shrink-0" aria-label="Remove food" @click="meal.items.splice(n, 1)"><Icon name="trash" class="w-4 h-4" /></button>
                        </div>
                    </div>

                    <input v-model="meal.note" type="text" maxlength="500" class="p-input py-2.5 mt-3" placeholder="Note for this meal (optional)" aria-label="Meal note">

                    <div class="flex flex-wrap items-center justify-between gap-2 mt-3">
                        <button type="button" class="p-btn-ghost" @click="meal.items.push(blankItem())"><Icon name="plus" class="w-4 h-4" /> Add food</button>
                        <div class="flex items-center gap-1">
                            <button type="button" class="p-2 text-warm-grey hover:text-gold-300 disabled:opacity-30" :disabled="m === 0" aria-label="Move meal up" @click="move(form.meals, m, -1)"><Icon name="up" class="w-4 h-4" /></button>
                            <button type="button" class="p-2 text-warm-grey hover:text-gold-300 disabled:opacity-30" :disabled="m === form.meals.length - 1" aria-label="Move meal down" @click="move(form.meals, m, 1)"><Icon name="down" class="w-4 h-4" /></button>
                            <button type="button" class="p-2 text-warm-grey hover:text-red-400 disabled:opacity-30" :disabled="form.meals.length === 1" aria-label="Remove meal" @click="form.meals.splice(m, 1)"><Icon name="trash" class="w-4 h-4" /></button>
                        </div>
                    </div>
                </fieldset>
            </div>

            <button type="button" class="p-btn-ghost mt-4" :disabled="form.meals.length >= 30" @click="form.meals.push(blankMeal(form.meals.length + 1))"><Icon name="plus" class="w-4 h-4" /> Add meal</button>

            <p v-if="hasErrors()" class="p-error">Please check the plan — every meal and food needs a name, and the targets must be realistic numbers.</p>
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 mt-6 pt-6 border-t border-white/5">
                <button type="button" class="p-btn-ghost py-3" @click="close">Cancel</button>
                <button type="submit" class="p-btn" :disabled="form.processing">{{ form.processing ? 'Saving…' : 'Save plan' }}</button>
            </div>
        </form>

        <div v-if="plans.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
            <article v-for="plan in plans" :key="plan.id" class="p-card p-5 sm:p-6 flex flex-col">
                <p class="p-eyebrow text-gold-400 mb-2">{{ audienceLabel(plan) }}</p>
                <h2 class="text-off-white font-semibold text-lg leading-snug break-words">{{ plan.title }}</h2>
                <p class="text-sm text-text-body mt-1">
                    {{ [plan.calories && `${plan.calories} kcal`, plan.protein_g != null && `P ${plan.protein_g} g`, `${plan.meals.length} ${plan.meals.length === 1 ? 'meal' : 'meals'}`].filter(Boolean).join(' · ') }}
                </p>
                <div class="flex items-center justify-between gap-3 mt-auto pt-5">
                    <p class="text-xs text-warm-grey">Updated {{ fmtDate(plan.updated_at) }}</p>
                    <div class="flex items-center gap-2">
                        <button type="button" class="p-btn-ghost px-3 py-2" @click="duplicate(plan)">Copy</button>
                        <button type="button" class="p-btn-ghost px-3 py-2" @click="open(plan)">Edit</button>
                        <button type="button" class="p-2 text-warm-grey hover:text-red-400 transition-colors" :aria-label="`Delete ${plan.title}`" @click="remove(plan)"><Icon name="trash" class="w-4 h-4" /></button>
                    </div>
                </div>
            </article>
        </div>
        <div v-else-if="!editing" class="p-card">
            <EmptyState icon="fire" title="No nutrition plans yet" text="Create a plan with targets and meals and assign it to your clients.">
                <button type="button" class="p-btn-ghost" @click="open()">Create the first plan</button>
            </EmptyState>
        </div>
    </PortalLayout>
</template>
