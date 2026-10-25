<script setup>
import { nextTick, onMounted, ref, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Icon from './Icon.vue';
import { fmtDateTime, initials } from '@/lib/format';

// Conversation view shared by the client portal and the coach admin.
const props = defineProps({
    messages: { type: Array, required: true },
    replyUrl: { type: String, required: true },
    closed: { type: Boolean, default: false },
});

const me = usePage().props.auth.user.id;
const form = useForm({ body: '' });
const end = ref(null);

const scrollToEnd = () => nextTick(() => end.value?.scrollIntoView({ block: 'nearest' }));
onMounted(scrollToEnd);
watch(() => props.messages.length, scrollToEnd);

function send() {
    form.post(props.replyUrl, { preserveScroll: true, onSuccess: () => form.reset() });
}
</script>

<template>
    <div class="p-card overflow-hidden">
        <div class="p-4 sm:p-6 space-y-5">
            <div v-for="message in messages" :key="message.id" class="flex gap-3" :class="message.user_id === me ? 'flex-row-reverse' : ''">
                <div class="w-9 h-9 rounded-full text-xs font-bold flex items-center justify-center flex-shrink-0" :class="message.user?.is_admin ? 'bg-gradient-to-br from-gold-400 to-gold-600 text-black' : 'bg-white/10 text-off-white'">
                    {{ initials(message.user?.name) }}
                </div>
                <div class="min-w-0 max-w-[85%] sm:max-w-[70%]" :class="message.user_id === me ? 'text-right' : ''">
                    <p class="text-xs text-warm-grey mb-1.5">
                        <span class="text-text-body font-medium">{{ message.user?.is_admin ? `Coach ${message.user.name.split(' ')[0]}` : message.user?.name }}</span>
                        · {{ fmtDateTime(message.created_at) }}
                    </p>
                    <div class="inline-block text-left px-4 py-3 rounded-2xl text-sm leading-relaxed whitespace-pre-wrap break-words" :class="message.user_id === me ? 'bg-gold-500/15 border border-gold-500/20 text-off-white rounded-tr-md' : 'bg-white/5 border border-white/5 text-off-white rounded-tl-md'">{{ message.body }}</div>
                </div>
            </div>
            <div ref="end"></div>
        </div>

        <form class="border-t border-white/5 p-4 sm:p-6 bg-dark/30" @submit.prevent="send">
            <p v-if="closed" class="text-sm text-warm-grey mb-3">This conversation is closed. Sending a message will reopen it.</p>
            <label for="reply" class="sr-only">Your message</label>
            <textarea id="reply" v-model="form.body" rows="3" required maxlength="5000" class="p-input resize-none" placeholder="Write your message…"></textarea>
            <p v-if="form.errors.body" class="p-error">{{ form.errors.body }}</p>
            <div class="flex justify-end mt-3">
                <button type="submit" class="p-btn" :disabled="form.processing || !form.body.trim()">
                    <Icon name="arrow" class="w-4 h-4" />
                    {{ form.processing ? 'Sending…' : 'Send' }}
                </button>
            </div>
        </form>
    </div>
</template>
