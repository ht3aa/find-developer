<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import ChatAttachment from '@/components/chat/ChatAttachment.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { formatMessageTime, getInitials } from '@/composables/useChat';
import { Copy, Reply } from 'lucide-vue-next';
import { ref } from 'vue';
import type { ChatMessage } from '@/types';

const props = defineProps<{
    message: ChatMessage;
}>();

const emit = defineEmits<{
    reply: [message: ChatMessage];
}>();

const copied = ref(false);
let copiedTimeout: ReturnType<typeof setTimeout> | null = null;

function stripHtml(html: string | null): string {
    if (!html) return '';
    const div = document.createElement('div');
    div.innerHTML = html;
    return div.textContent?.trim() ?? '';
}

async function copyMessage(): Promise<void> {
    const text = stripHtml(props.message.body);
    if (!text) return;
    try {
        await navigator.clipboard.writeText(text);
        copied.value = true;
        if (copiedTimeout) clearTimeout(copiedTimeout);
        copiedTimeout = setTimeout(() => {
            copied.value = false;
        }, 1500);
    } catch {
        // ignore
    }
}
</script>

<template>
    <div
        class="flex gap-3"
        :class="message.is_own ? 'flex-row-reverse' : 'flex-row'"
    >
        <Avatar class="mt-1 size-8 shrink-0">
            <AvatarFallback
                class="text-xs font-medium"
                :class="
                    message.is_own
                        ? 'bg-primary/10 text-primary'
                        : 'bg-muted text-muted-foreground'
                "
            >
                {{ getInitials(message.user.name) }}
            </AvatarFallback>
        </Avatar>

        <div
            class="max-w-[75%] space-y-1"
            :class="message.is_own ? 'items-end' : 'items-start'"
        >
            <div
                class="flex flex-wrap items-center gap-2"
                :class="message.is_own ? 'flex-row-reverse' : 'flex-row'"
            >
                <Link
                    v-if="!message.is_own && message.user.developer_slug"
                    :href="`/developers/${message.user.developer_slug}`"
                    class="text-xs font-medium text-muted-foreground underline-offset-4 hover:text-foreground hover:underline"
                >
                    {{ message.user.name }}
                </Link>
                <span
                    v-else
                    class="text-xs font-medium text-muted-foreground"
                >
                    {{ message.is_own ? 'You' : message.user.name }}
                </span>
                <Badge
                    v-if="!message.is_own && message.user.user_type_label"
                    variant="secondary"
                    class="text-[10px] font-normal"
                >
                    {{ message.user.user_type_label }}
                </Badge>
                <span class="text-[10px] text-muted-foreground/70">
                    {{ formatMessageTime(message.created_at) }}
                </span>
            </div>

            <div
                v-if="message.reply_to"
                class="mb-2 border-l-2 border-muted-foreground/30 pl-3 text-xs text-muted-foreground [&_a]:text-primary [&_a]:underline"
            >
                <Link
                    v-if="message.reply_to.user.developer_slug"
                    :href="`/developers/${message.reply_to.user.developer_slug}`"
                    class="font-medium underline-offset-4 hover:underline"
                >
                    {{ message.reply_to.user.name }}
                </Link>
                <span
                    v-else
                    class="font-medium"
                >
                    {{ message.reply_to.user.name }}
                </span>
                <div
                    v-if="message.reply_to.body"
                    class="prose prose-sm dark:prose-invert max-w-none mt-0.5 [&_p]:my-0 [&_ul]:my-1 [&_ol]:my-1"
                    v-html="message.reply_to.body"
                />
            </div>
            <div
                v-if="message.body"
                class="prose prose-sm dark:prose-invert max-w-none rounded-2xl px-4 py-2"
                :class="
                    message.is_own
                        ? 'rounded-tr-sm bg-primary text-primary-foreground [&_a]:text-primary-foreground/90 [&_a]:underline [&_code]:bg-primary-foreground/20 [&_code]:text-primary-foreground'
                        : 'rounded-tl-sm bg-muted'
                "
                v-html="message.body"
            />

            <div
                v-if="message.attachments.length > 0"
                class="space-y-2"
                :class="message.is_own ? 'ml-auto' : ''"
            >
                <ChatAttachment
                    v-for="attachment in message.attachments"
                    :key="attachment.id"
                    :attachment="attachment"
                />
            </div>

            <div class="mt-1 flex items-center gap-3">
                <button
                    v-if="message.body"
                    type="button"
                    class="flex items-center gap-1 text-[10px] text-muted-foreground transition-colors hover:text-foreground"
                    :title="copied ? 'Copied!' : 'Copy'"
                    @click="copyMessage"
                >
                    <Copy class="size-3" />
                    {{ copied ? 'Copied!' : 'Copy' }}
                </button>
                <button
                    type="button"
                    class="flex items-center gap-1 text-[10px] text-muted-foreground transition-colors hover:text-foreground"
                    @click="emit('reply', message)"
                >
                    <Reply class="size-3" />
                    Reply
                </button>
            </div>
        </div>
    </div>
</template>
