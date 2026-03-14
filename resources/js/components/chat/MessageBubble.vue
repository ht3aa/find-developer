<script setup lang="ts">
import ChatAttachment from '@/components/chat/ChatAttachment.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { formatMessageTime, getInitials } from '@/composables/useChat';
import type { ChatMessage } from '@/types';

defineProps<{
    message: ChatMessage;
}>();
</script>

<template>
    <div
        class="flex gap-3"
        :class="message.is_own ? 'flex-row-reverse' : 'flex-row'"
    >
        <Avatar class="mt-1 size-8 shrink-0">
            <AvatarFallback
                class="text-xs font-medium"
                :class="message.is_own ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground'"
            >
                {{ getInitials(message.user.name) }}
            </AvatarFallback>
        </Avatar>

        <div
            class="max-w-[75%] space-y-1"
            :class="message.is_own ? 'items-end' : 'items-start'"
        >
            <div class="flex items-center gap-2" :class="message.is_own ? 'flex-row-reverse' : 'flex-row'">
                <span class="text-xs font-medium text-muted-foreground">
                    {{ message.is_own ? 'You' : message.user.name }}
                </span>
                <span class="text-[10px] text-muted-foreground/70">
                    {{ formatMessageTime(message.created_at) }}
                </span>
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
        </div>
    </div>
</template>
