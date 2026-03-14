<script setup lang="ts">
import { Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import ConversationItem from '@/components/chat/ConversationItem.vue';
import { Input } from '@/components/ui/input';
import { Skeleton } from '@/components/ui/skeleton';
import type { ChatConversation } from '@/types';

const props = defineProps<{
    conversations: ChatConversation[];
    activeConversationId: number | null;
    loading?: boolean;
}>();

const emit = defineEmits<{
    select: [conversationId: number];
}>();

const searchQuery = ref('');

const filtered = computed(() => {
    if (!searchQuery.value) return props.conversations;
    const q = searchQuery.value.toLowerCase();
    return props.conversations.filter((c) =>
        c.participant?.name.toLowerCase().includes(q),
    );
});
</script>

<template>
    <div class="flex h-full flex-col">
        <div class="p-3">
            <div class="relative">
                <Search
                    class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="searchQuery"
                    placeholder="Search conversations..."
                    class="pl-9"
                />
            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-2">
            <template v-if="loading">
                <div
                    v-for="i in 5"
                    :key="i"
                    class="flex items-center gap-3 rounded-lg px-3 py-3"
                >
                    <Skeleton class="size-10 shrink-0 rounded-full" />
                    <div class="min-w-0 flex-1 space-y-2">
                        <Skeleton class="h-4 w-3/4" />
                        <Skeleton class="h-3 w-1/2" />
                    </div>
                </div>
            </template>
            <template v-else>
                <div
                    v-if="filtered.length === 0"
                    class="px-3 py-8 text-center text-sm text-muted-foreground"
                >
                    {{
                        searchQuery
                            ? 'No conversations found'
                            : 'No conversations yet'
                    }}
                </div>
                <ConversationItem
                    v-for="conversation in filtered"
                    :key="conversation.id"
                    :conversation="conversation"
                    :is-active="conversation.id === activeConversationId"
                    @click="emit('select', conversation.id)"
                />
            </template>
        </div>
    </div>
</template>
