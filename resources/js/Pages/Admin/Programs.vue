<script setup>
import { computed, nextTick, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import PageHeader from '@/components/portal/PageHeader.vue';
import EmptyState from '@/components/portal/EmptyState.vue';
import Icon from '@/components/portal/Icon.vue';
import { fmtDate } from '@/lib/format';
import { planWeeks } from '@/lib/plan';

const props = defineProps({
    plans: { type: Array, default: () => [] },
    clients: { type: Array, default: () => [] },
    packages: { type: Array, default: () => [] },
    videos: { type: Array, default: () => [] },
});

// { Chest: [videos…], Back: [...] } for the video picker
const videosByCategory = computed(() => props.videos.reduce((groups, video) => {
    (groups[video.category] ??= []).push(video);
    return groups;
}, {}));

const blankExercise = () => ({ name: '', sets: '', reps: '', rest: '', note: '', video_id: null });
const blankDay = (n, week = null) => ({ name: `Day ${n}`, week, exercises: [blankExercise()] });

const editing = ref(null); // null = closed, 'new' or a plan id
const editor = ref(null);
// audience: 'all', 'package:ID' or 'client:ID'
const form = useForm({ title: '', description: '', audience: 'all', days: [blankDay(1)] });

// Programs split into weeks are edited one week at a time.
const editWeek = ref(null);
const formWeeks = computed(() => planWeeks(form));
const lastWeek = computed(() => formWeeks.value[formWeeks.value.length - 1] ?? 1);
// The days on screen, each with its position in form.days.
const shownDays = computed(() => form.days
    .map((day, index) => ({ day, index }))
    .filter(({ day }) => !formWeeks.value.length || day.week === editWeek.value));

function addDay() {
    const shown = shownDays.value;
    const after = shown.length ? shown[shown.length - 1].index : form.days.length - 1;
    form.days.splice(after + 1, 0, blankDay(shown.length + 1, editWeek.value));
}

// The first time, the existing days become week 1.
function addWeek() {
    if (!formWeeks.value.length) form.days.forEach(day => { day.week = 1; });
    const week = lastWeek.value + 1;
    form.days.push(blankDay(1, week));
    editWeek.value = week;
}

// Swap a day with its neighbour in the same week.
function moveDay(position, by) {
    const target = shownDays.value[position + by];
    if (!target) return;
    const from = shownDays.value[position].index;
    [form.days[from], form.days[target.index]] = [form.days[target.index], form.days[from]];
}

function removeDay(index) {
    form.days.splice(index, 1);
    if (formWeeks.value.length && !formWeeks.value.includes(editWeek.value)) editWeek.value = lastWeek.value;
}

// Weeks containing a day with a validation error, so the coach can find it.
const weeksWithErrors = computed(() => new Set(Object.keys(form.errors)
    .map(key => form.days[key.match(/^days\.(\d+)\./)?.[1]]?.week)
    .filter(Boolean)));

function open(plan = null) {
    editing.value = plan ? plan.id : 'new';
    form.clearErrors();
    form.title = plan?.title ?? '';
    form.description = plan?.description ?? '';
    form.audience = plan?.user_id ? `client:${plan.user_id}` : plan?.program_id ? `package:${plan.program_id}` : 'all';
    form.days = plan
        ? plan.days.map(day => ({ name: day.name, week: day.week ?? null, exercises: day.exercises.map(exercise => ({ ...blankExercise(), ...exercise })) }))
        : [blankDay(1)];
    editWeek.value = formWeeks.value[0] ?? null;
    nextTick(() => editor.value?.scrollIntoView({ behavior: 'smooth', block: 'start' }));
}

function duplicate(plan) {
    open(plan);
    editing.value = 'new';
    form.title = `${plan.title} (copy)`;
}

function close() {
    editing.value = null;
    form.reset();
}

function move(list, index, by) {
    const target = index + by;
    if (target < 0 || target >= list.length) return;
    list.splice(target, 0, list.splice(index, 1)[0]);
}

function save() {
    const [kind, id] = form.audience.split(':');
    form.transform(data => ({
        title: data.title,
        description: data.description || null,
        user_id: kind === 'client' ? Number(id) : null,
        program_id: kind === 'package' ? Number(id) : null,
        days: data.days,
    }));
    const options = { preserveScroll: true, onSuccess: close };
    if (editing.value === 'new') form.post('/admin/programs', options);
    else form.put(`/admin/programs/${editing.value}`, options);
}

function remove(plan) {
    if (confirm(`Delete "${plan.title}"? Clients will no longer see it.`)) {
        router.delete(`/admin/programs/${plan.id}`, { preserveScroll: true, onSuccess: () => editing.value === plan.id && close() });
    }
}

const audienceLabel = plan => (plan.user ? plan.user.name : plan.program ? `${plan.program.title} package` : 'All clients');
const exerciseCount = plan => plan.days.reduce((sum, day) => sum + day.exercises.length, 0);
const weekCount = plan => planWeeks(plan).length;
const hasErrors = () => Object.keys(form.errors).length > 0;
</script>

<template>
    <PortalLayout title="Training programs">
        <PageHeader eyebrow="Coach admin" title="Training programs" subtitle="Build a program once and give it to one client, everyone on a package, or all clients.">
            <button v-if="!editing" type="button" class="p-btn w-full sm:w-auto" @click="open()">
                <Icon name="plus" class="w-4 h-4" /> New program
            </button>
        </PageHeader>

        <form v-if="editing" ref="editor" class="p-card border-gold-500/30 p-4 sm:p-8 mb-8 scroll-mt-20" @submit.prevent="save">
            <h2 class="font-display text-xl uppercase text-off-white mb-5">{{ editing === 'new' ? 'New program' : 'Edit program' }}</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="plan-title" class="p-label">Program name</label>
                    <input id="plan-title" v-model="form.title" type="text" required maxlength="255" class="p-input" placeholder="e.g. Upper / Lower — 4 days">
                    <p v-if="form.errors.title" class="p-error">{{ form.errors.title }}</p>
                </div>
                <div>
                    <label for="plan-audience" class="p-label">Who is it for?</label>
                    <select id="plan-audience" v-model="form.audience" class="p-input">
                        <option value="all">All clients</option>
                        <optgroup label="Everyone on a package">
                            <option v-for="item in packages" :key="item.id" :value="`package:${item.id}`">{{ item.title }} package</option>
                        </optgroup>
                        <optgroup v-if="clients.length" label="One client">
                            <option v-for="item in clients" :key="item.id" :value="`client:${item.id}`">{{ item.name }}</option>
                        </optgroup>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="plan-description" class="p-label">Notes for the client (optional)</label>
                    <textarea id="plan-description" v-model="form.description" rows="3" maxlength="5000" class="p-input" placeholder="How to run the program, progression, steps, etc."></textarea>
                </div>
            </div>

            <div v-if="formWeeks.length" class="flex flex-wrap gap-2 mt-6" role="tablist" aria-label="Week">
                <button
                    v-for="week in formWeeks"
                    :key="week"
                    type="button"
                    role="tab"
                    :aria-selected="week === editWeek"
                    class="px-4 py-2 rounded-full border text-sm font-semibold transition-colors tabular-nums"
                    :class="[week === editWeek ? 'bg-gold-500 border-gold-500 text-black' : 'border-white/10 text-off-white hover:border-gold-500/40', weeksWithErrors.has(week) && week !== editWeek ? 'border-red-400/60' : '']"
                    @click="editWeek = week"
                >
                    Week {{ week }}
                </button>
            </div>

            <div class="space-y-5 mt-6">
                <fieldset v-for="({ day, index: d }, p) in shownDays" :key="d" class="rounded-2xl border border-white/10 p-3 sm:p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <label :for="`day-${d}`" class="sr-only">Day name</label>
                        <input :id="`day-${d}`" v-model="day.name" type="text" required maxlength="100" class="p-input font-semibold" placeholder="e.g. Day 1 — Upper">
                        <button type="button" class="p-2 text-warm-grey hover:text-gold-300 disabled:opacity-30" :disabled="p === 0" aria-label="Move day up" @click="moveDay(p, -1)"><Icon name="up" class="w-4 h-4" /></button>
                        <button type="button" class="p-2 text-warm-grey hover:text-gold-300 disabled:opacity-30" :disabled="p === shownDays.length - 1" aria-label="Move day down" @click="moveDay(p, 1)"><Icon name="down" class="w-4 h-4" /></button>
                        <button type="button" class="p-2 text-warm-grey hover:text-red-400 disabled:opacity-30" :disabled="form.days.length === 1" aria-label="Remove day" @click="removeDay(d)"><Icon name="trash" class="w-4 h-4" /></button>
                    </div>

                    <div class="space-y-3">
                        <div v-for="(exercise, e) in day.exercises" :key="e" class="rounded-xl bg-dark/40 border border-white/5 p-3">
                            <div class="grid grid-cols-6 lg:grid-cols-12 gap-2">
                                <input v-model="exercise.name" type="text" required maxlength="150" class="p-input py-2.5 col-span-6 lg:col-span-4" placeholder="Exercise" :aria-label="`Exercise ${e + 1} name`">
                                <input v-model="exercise.sets" type="text" maxlength="20" class="p-input py-2.5 col-span-2 lg:col-span-1 px-3" placeholder="Sets" aria-label="Sets">
                                <input v-model="exercise.reps" type="text" maxlength="30" class="p-input py-2.5 col-span-2 lg:col-span-2 px-3" placeholder="Reps" aria-label="Reps">
                                <input v-model="exercise.rest" type="text" maxlength="30" class="p-input py-2.5 col-span-2 lg:col-span-2 px-3" placeholder="Rest" aria-label="Rest">
                                <select v-model="exercise.video_id" class="p-input py-2.5 col-span-6 lg:col-span-3 px-3" aria-label="Video">
                                    <option :value="null">No video</option>
                                    <optgroup v-for="(items, category) in videosByCategory" :key="category" :label="category">
                                        <option v-for="video in items" :key="video.id" :value="video.id">{{ video.title }}</option>
                                    </optgroup>
                                </select>
                                <input v-model="exercise.note" type="text" maxlength="300" class="p-input py-2.5 col-span-6 lg:col-span-9" placeholder="Note / cue (optional)" aria-label="Note">
                                <div class="col-span-6 lg:col-span-3 flex items-center justify-end gap-1">
                                    <button type="button" class="p-2 text-warm-grey hover:text-gold-300 disabled:opacity-30" :disabled="e === 0" aria-label="Move exercise up" @click="move(day.exercises, e, -1)"><Icon name="up" class="w-4 h-4" /></button>
                                    <button type="button" class="p-2 text-warm-grey hover:text-gold-300 disabled:opacity-30" :disabled="e === day.exercises.length - 1" aria-label="Move exercise down" @click="move(day.exercises, e, 1)"><Icon name="down" class="w-4 h-4" /></button>
                                    <button type="button" class="p-2 text-warm-grey hover:text-red-400" aria-label="Remove exercise" @click="day.exercises.splice(e, 1)"><Icon name="trash" class="w-4 h-4" /></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="p-btn-ghost mt-3" @click="day.exercises.push(blankExercise())"><Icon name="plus" class="w-4 h-4" /> Add exercise</button>
                </fieldset>
            </div>

            <div class="flex flex-wrap gap-3 mt-4">
                <button type="button" class="p-btn-ghost" :disabled="form.days.length >= 100" @click="addDay"><Icon name="plus" class="w-4 h-4" /> Add day</button>
                <button type="button" class="p-btn-ghost" :disabled="form.days.length >= 100" @click="addWeek"><Icon name="plus" class="w-4 h-4" /> Add week {{ formWeeks.length ? lastWeek + 1 : 2 }}</button>
            </div>

            <p v-if="hasErrors()" class="p-error">Please check the program — every day and exercise needs a name.</p>
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 mt-6 pt-6 border-t border-white/5">
                <button type="button" class="p-btn-ghost py-3" @click="close">Cancel</button>
                <button type="submit" class="p-btn" :disabled="form.processing">{{ form.processing ? 'Saving…' : 'Save program' }}</button>
            </div>
        </form>

        <div v-if="plans.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
            <article v-for="plan in plans" :key="plan.id" class="p-card p-5 sm:p-6 flex flex-col">
                <p class="p-eyebrow text-gold-400 mb-2">{{ audienceLabel(plan) }}</p>
                <h2 class="text-off-white font-semibold text-lg leading-snug break-words">{{ plan.title }}</h2>
                <p class="text-sm text-text-body mt-1"><template v-if="weekCount(plan)">{{ weekCount(plan) }} weeks · </template>{{ plan.days.length }} {{ plan.days.length === 1 ? 'day' : 'days' }} · {{ exerciseCount(plan) }} exercises</p>
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
            <EmptyState icon="dumbbell" title="No programs yet" text="Create a training program and assign it to your clients.">
                <button type="button" class="p-btn-ghost" @click="open()">Create the first program</button>
            </EmptyState>
        </div>
    </PortalLayout>
</template>
