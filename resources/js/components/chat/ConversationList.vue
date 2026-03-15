<script setup lang="ts">
import { ChevronDown, Search } from 'lucide-vue-next';
import {
    computed,
    nextTick,
    onBeforeUnmount,
    onMounted,
    ref,
    watch,
} from 'vue';
import ConversationItem from '@/components/chat/ConversationItem.vue';
import { Input } from '@/components/ui/input';
import { Skeleton } from '@/components/ui/skeleton';
import type { ChatConversation } from '@/types';

const props = withDefaults(
    defineProps<{
        conversations: ChatConversation[];
        activeConversationId: number | null;
        loading?: boolean;
        hasMore?: boolean;
        loadingMore?: boolean;
    }>(),
    { hasMore: false, loadingMore: false },
);

const emit = defineEmits<{
    select: [conversationId: number];
    loadMore: [];
}>();

const scrollContainer = ref<HTMLElement | null>(null);
const loadMoreSentinel = ref<HTMLElement | null>(null);
const searchQuery = ref('');

const filtered = computed(() => {
    if (!searchQuery.value) return props.conversations;
    const q = searchQuery.value.toLowerCase();
    return props.conversations.filter((c) =>
        c.participant?.name.toLowerCase().includes(q),
    );
});

let observer: IntersectionObserver | null = null;

function setupObserver() {
    observer?.disconnect();
    if (
        !loadMoreSentinel.value ||
        !scrollContainer.value ||
        props.loading ||
        !props.hasMore
    ) {
        return;
    }

    observer = new IntersectionObserver(
        (entries) => {
            if (props.loadingMore || !props.hasMore || props.loading) return;
            const entry = entries[0];
            if (entry?.isIntersecting) {
                emit('loadMore');
            }
        },
        {
            root: scrollContainer.value,
            rootMargin: '100px',
            threshold: 0,
        },
    );
    observer.observe(loadMoreSentinel.value);
}

function trySetupObserver() {
    nextTick(() => {
        if (loadMoreSentinel.value && scrollContainer.value) {
            setupObserver();
        }
    });
}

onMounted(trySetupObserver);

onBeforeUnmount(() => {
    observer?.disconnect();
});

watch(
    () => [props.hasMore, props.loading, filtered.value.length],
    trySetupObserver,
);
</script>

<template>
    <div class="flex max-h-[60vh] flex-col">
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

        <div class="relative min-h-0 flex-1">
            <div
                ref="scrollContainer"
                class="h-full overflow-y-auto px-2"
            >
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
                <div
                    v-if="hasMore && !searchQuery"
                    ref="loadMoreSentinel"
                    class="h-4 shrink-0"
                    aria-hidden="true"
                />
                <div
                    v-if="loadingMore"
                    class="flex items-center justify-center py-3 text-sm text-muted-foreground"
                >
                    Loading more...
                </div>
            </template>
            </div>

            <!-- Scroll-for-more indicator (purely visual, non-interactive) -->
            <div
                v-if="hasMore && !searchQuery && !loading && filtered.length > 0 && !loadingMore"
                class="pointer-events-none select-none absolute bottom-0 left-0 right-0 flex flex-col items-center bg-gradient-to-t from-background via-background/95 to-transparent pt-6 pb-2"
                role="presentation"
                aria-hidden="true"
                tabindex="-1"
            >
                <span class="flex items-center gap-1 text-xs text-muted-foreground">
                    <ChevronDown class="size-3.5 animate-bounce" />
                    Scroll for more
                </span>
            </div>
        </div>
    </div>
</template>
