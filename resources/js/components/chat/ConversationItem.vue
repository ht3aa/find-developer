<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { formatRelativeTime, getInitials } from '@/composables/useChat';
import type { ChatConversation } from '@/types';

defineProps<{
    conversation: ChatConversation;
    isActive: boolean;
}>();

function stripHtml(html: string | null): string {
    if (!html) return '';
    return html.replace(/<[^>]*>/g, '').slice(0, 80);
}
</script>

<template>
    <button
        class="flex w-full items-center gap-3 rounded-lg px-3 py-3 text-left transition-colors hover:bg-accent"
        :class="{ 'bg-accent': isActive }"
    >
        <Avatar class="size-10 shrink-0">
            <AvatarFallback
                class="bg-primary/10 text-sm font-medium text-primary"
            >
                {{
                    conversation.participant
                        ? getInitials(conversation.participant.name)
                        : '?'
                }}
            </AvatarFallback>
        </Avatar>

        <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between gap-2">
                <div class="flex min-w-0 items-center gap-1.5">
                    <Link
                        v-if="conversation.participant?.developer_slug"
                        :href="`/developers/${conversation.participant.developer_slug}`"
                        class="truncate text-sm font-medium text-foreground underline-offset-4 hover:underline"
                        @click.stop
                    >
                        {{ conversation.participant.name }}
                    </Link>
                    <span
                        v-else
                        class="truncate text-sm font-medium text-foreground"
                    >
                        {{ conversation.participant?.name ?? 'Unknown' }}
                    </span>
                    <span
                        v-if="conversation.participant?.user_type_label"
                        class="shrink-0 rounded-full bg-muted px-1.5 py-0.5 text-[10px] font-medium text-muted-foreground"
                    >
                        {{ conversation.participant.user_type_label }}
                    </span>
                </div>
                <span
                    v-if="conversation.last_message"
                    class="shrink-0 text-xs text-muted-foreground"
                >
                    {{
                        formatRelativeTime(conversation.last_message.created_at)
                    }}
                </span>
            </div>
            <div class="flex items-center justify-between gap-2">
                <p class="truncate text-xs text-muted-foreground">
                    <span
                        v-if="conversation.last_message?.is_own"
                        class="text-muted-foreground"
                        >You:
                    </span>
                    {{
                        stripHtml(conversation.last_message?.body) ||
                        'Attachment'
                    }}
                </p>
                <span
                    v-if="conversation.unread_count > 0"
                    class="flex size-5 shrink-0 items-center justify-center rounded-full bg-primary text-[10px] font-bold text-primary-foreground"
                >
                    {{
                        conversation.unread_count > 9
                            ? '9+'
                            : conversation.unread_count
                    }}
                </span>
            </div>
        </div>
    </button>
</template>
