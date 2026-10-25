<script setup>
import { computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import PageHeader from '@/components/portal/PageHeader.vue';
import Icon from '@/components/portal/Icon.vue';
import { isInherit } from '@/data/packages';

defineProps({ program: { type: Object, default: null } });

const user = computed(() => usePage().props.auth.user);
const form = useForm({ current_password: '', password: '', password_confirmation: '' });

function submit() {
    form.put('/portal/account/password', { preserveScroll: true, onSuccess: () => form.reset() });
}
</script>

<template>
    <PortalLayout title="Account">
        <PageHeader eyebrow="Settings" title="Your account" />

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            <div class="space-y-4 sm:space-y-6">
                <section class="p-card p-6 sm:p-8">
                    <h2 class="font-display text-xl uppercase text-off-white mb-5">Profile</h2>
                    <dl class="space-y-4 text-sm">
                        <div><dt class="text-warm-grey">Name</dt><dd class="text-off-white mt-0.5">{{ user.name }}</dd></div>
                        <div><dt class="text-warm-grey">Login email</dt><dd class="text-off-white mt-0.5 break-all">{{ user.email }}</dd></div>
                    </dl>
                    <p class="text-xs text-warm-grey mt-5">Need to change your name or email? <Link href="/portal/support" class="text-gold-400 hover:text-gold-300">Message your coach</Link>.</p>
                </section>

                <section v-if="program" class="p-card p-6 sm:p-8">
                    <p class="p-eyebrow text-gold-400 mb-2">Your package</p>
                    <h2 class="font-display text-2xl uppercase text-off-white">{{ program.title }}</h2>
                    <p class="text-text-body text-sm mt-1 mb-5">{{ program.excerpt }}</p>
                    <ul class="space-y-2.5">
                        <li v-for="feature in program.features" :key="feature" class="flex items-start gap-2 text-sm" :class="isInherit(feature) ? 'text-off-white font-semibold' : 'text-text-body'">
                            <Icon v-if="!isInherit(feature)" name="check" class="w-4 h-4 text-gold-500 flex-shrink-0 mt-0.5" />
                            {{ feature }}
                        </li>
                    </ul>
                </section>
            </div>

            <form class="p-card p-6 sm:p-8 self-start" @submit.prevent="submit">
                <h2 class="font-display text-xl uppercase text-off-white mb-2">Change password</h2>
                <p class="text-text-body text-sm mb-5">Use at least 8 characters.</p>
                <div class="space-y-4">
                    <div>
                        <label for="current_password" class="p-label">Current password</label>
                        <input id="current_password" v-model="form.current_password" type="password" required autocomplete="current-password" class="p-input">
                        <p v-if="form.errors.current_password" class="p-error">{{ form.errors.current_password }}</p>
                    </div>
                    <div>
                        <label for="password" class="p-label">New password</label>
                        <input id="password" v-model="form.password" type="password" required minlength="8" autocomplete="new-password" class="p-input">
                        <p v-if="form.errors.password" class="p-error">{{ form.errors.password }}</p>
                    </div>
                    <div>
                        <label for="password_confirmation" class="p-label">Repeat new password</label>
                        <input id="password_confirmation" v-model="form.password_confirmation" type="password" required minlength="8" autocomplete="new-password" class="p-input">
                    </div>
                </div>
                <button type="submit" class="p-btn w-full sm:w-auto mt-6" :disabled="form.processing">{{ form.processing ? 'Saving…' : 'Update password' }}</button>
            </form>
        </div>
    </PortalLayout>
</template>
