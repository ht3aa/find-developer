<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { ArrowLeft, MessageCircle } from 'lucide-vue-next';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import ConversationList from '@/components/chat/ConversationList.vue';
import MessageBubble from '@/components/chat/MessageBubble.vue';
import MessageComposer from '@/components/chat/MessageComposer.vue';
import NewConversationDialog from '@/components/chat/NewConversationDialog.vue';
import Navbar from '@/components/Navbar.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { getInitials } from '@/composables/useChat';
import type { ChatConversation, ChatMessage, ChatUserSummary } from '@/types';

const props = defineProps<{
    conversations: ChatConversation[];
    selectedConversationId: number | null;
    messages: ChatMessage[];
    selectedParticipant: ChatUserSummary | null;
}>();

const page = usePage();
const messagesContainer = ref<HTMLElement | null>(null);
const isSending = ref(false);
const mobileShowChat = ref(!!props.selectedConversationId);

const activeConversation = computed(() =>
    props.conversations.find((c) => c.id === props.selectedConversationId),
);

function selectConversation(conversationId: number) {
    mobileShowChat.value = true;
    router.get(`/messages/${conversationId}`, {}, { preserveState: false });
}

function goBackToList() {
    mobileShowChat.value = false;
    router.get('/messages', {}, { preserveState: false });
}

function scrollToBottom() {
    nextTick(() => {
        if (!messagesContainer.value) return;
        messagesContainer.value.scrollTo({
            top: messagesContainer.value.scrollHeight,
            behavior: 'instant',
        });
    });
}

function handleSend(payload: { body: string; attachments: File[] }) {
    if (!props.selectedConversationId) return;

    isSending.value = true;

    const formData = new FormData();
    if (payload.body) {
        formData.append('body', payload.body);
    }
    payload.attachments.forEach((file, i) => {
        formData.append(`attachments[${i}]`, file);
    });

    router.post(`/messages/${props.selectedConversationId}`, formData, {
        forceFormData: true,
        preserveScroll: true,
        onFinish: () => {
            isSending.value = false;
            scrollToBottom();
        },
    });
}

onMounted(() => {
    scrollToBottom();

    if (props.selectedConversationId) {
        startPolling();
    }
});

watch(
    () => props.selectedConversationId,
    (newId) => {
        if (newId) {
            scrollToBottom();
            startPolling();
        } else {
            stopPolling();
        }
    },
);

let pollInterval: ReturnType<typeof setInterval> | null = null;

function startPolling() {
    stopPolling();
    pollInterval = setInterval(() => {
        if (props.selectedConversationId) {
            router.reload({
                only: ['conversations', 'messages'],
                preserveScroll: true,
                onSuccess: () => {
                    const container = messagesContainer.value;
                    if (!container) return;
                    const { scrollTop, scrollHeight, clientHeight } = container;
                    const isNearBottom =
                        scrollHeight - scrollTop - clientHeight < 100;
                    if (isNearBottom) {
                        scrollToBottom();
                    }
                },
            });
        }
    }, 5000);
}

function stopPolling() {
    if (pollInterval) {
        clearInterval(pollInterval);
        pollInterval = null;
    }
}

const unreadTotal = computed(() =>
    props.conversations.reduce((sum, c) => sum + c.unread_count, 0),
);
</script>

<template>
    <Head>
        <title>Messages{{ unreadTotal > 0 ? ` (${unreadTotal})` : '' }}</title>
    </Head>

    <div class="flex h-screen flex-col bg-background">
        <Navbar />

        <div class="mx-auto flex w-full max-w-7xl flex-1 overflow-hidden">
            <!-- Conversation list sidebar -->
            <aside
                class="w-full flex-col border-r md:flex md:w-80 lg:w-96"
                :class="
                    mobileShowChat && selectedConversationId ? 'hidden' : 'flex'
                "
            >
                <div
                    class="flex items-center justify-between border-b px-4 py-3"
                >
                    <h1 class="text-lg font-semibold">Messages</h1>
                    <NewConversationDialog />
                </div>
                <ConversationList
                    :conversations="conversations"
                    :active-conversation-id="selectedConversationId"
                    @select="selectConversation"
                />
            </aside>

            <!-- Chat area -->
            <main
                class="flex-1 flex-col"
                :class="
                    !mobileShowChat && selectedConversationId === null
                        ? 'hidden md:flex'
                        : 'flex'
                "
            >
                <template v-if="selectedConversationId && selectedParticipant">
                    <!-- Chat header -->
                    <div class="flex items-center gap-3 border-b px-4 py-3">
                        <Button
                            variant="ghost"
                            size="icon"
                            class="h-8 w-8 md:hidden"
                            @click="goBackToList"
                        >
                            <ArrowLeft class="size-4" />
                        </Button>
                        <Avatar class="size-9">
                            <AvatarFallback
                                class="bg-primary/10 text-sm font-medium text-primary"
                            >
                                {{ getInitials(selectedParticipant.name) }}
                            </AvatarFallback>
                        </Avatar>
                        <div>
                            <p class="text-sm font-medium">
                                {{ selectedParticipant.name }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ selectedParticipant.email }}
                            </p>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div
                        ref="messagesContainer"
                        class="flex-1 space-y-4 overflow-y-auto px-4 py-4"
                    >
                        <div
                            v-if="messages.length === 0"
                            class="flex h-full items-center justify-center"
                        >
                            <p class="text-sm text-muted-foreground">
                                No messages yet. Start the conversation!
                            </p>
                        </div>
                        <MessageBubble
                            v-for="message in messages"
                            :key="message.id"
                            :message="message"
                        />
                    </div>

                    <!-- Composer -->
                    <MessageComposer :disabled="isSending" @send="handleSend" />
                </template>

                <!-- Empty state -->
                <div
                    v-else
                    class="flex h-full flex-col items-center justify-center gap-4 p-8"
                >
                    <div
                        class="flex size-16 items-center justify-center rounded-full bg-muted"
                    >
                        <MessageCircle class="size-8 text-muted-foreground" />
                    </div>
                    <div class="text-center">
                        <h2 class="text-lg font-medium">Your Messages</h2>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Select a conversation or start a new one
                        </p>
                    </div>
                    <NewConversationDialog />
                </div>
            </main>
        </div>
    </div>
</template>
