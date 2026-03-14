<script setup lang="ts">
import { Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import ConversationItem from '@/components/chat/ConversationItem.vue';
import { Input } from '@/components/ui/input';
import type { ChatConversation } from '@/types';

const props = defineProps<{
    conversations: ChatConversation[];
    activeConversationId: number | null;
}>();

const emit = defineEmits<{
    select: [conversationId: number];
}>();

const searchQuery = ref('');

const filtered = computed(() => {
    if (!searchQuery.value) return props.conversations;
    const q = searchQuery.value.toLowerCase();
    return props.conversations.filter(
        (c) => c.participant?.name.toLowerCase().includes(q),
    );
});
</script>

<template>
    <div class="flex h-full flex-col">
        <div class="p-3">
            <div class="relative">
                <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                    v-model="searchQuery"
                    placeholder="Search conversations..."
                    class="pl-9"
                />
            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-2">
            <div v-if="filtered.length === 0" class="px-3 py-8 text-center text-sm text-muted-foreground">
                {{ searchQuery ? 'No conversations found' : 'No conversations yet' }}
            </div>
            <ConversationItem
                v-for="conversation in filtered"
                :key="conversation.id"
                :conversation="conversation"
                :is-active="conversation.id === activeConversationId"
                @click="emit('select', conversation.id)"
            />
        </div>
    </div>
</template>
