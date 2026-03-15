<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, MessageCircle, ShieldCheck } from 'lucide-vue-next';
import {
    computed,
    nextTick,
    onBeforeUnmount,
    onMounted,
    ref,
    watch,
} from 'vue';
import ConversationList from '@/components/chat/ConversationList.vue';
import MessageBubble from '@/components/chat/MessageBubble.vue';
import MessageComposer from '@/components/chat/MessageComposer.vue';
import NewConversationDialog from '@/components/chat/NewConversationDialog.vue';
import Navbar from '@/components/Navbar.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { getInitials } from '@/composables/useChat';
import type { ChatConversation, ChatMessage, ChatUserSummary } from '@/types';

interface MessagesPayload {
    data: ChatMessage[];
    has_more: boolean;
}

const props = defineProps<{
    selectedConversationId: number | null;
    messages: MessagesPayload;
    selectedParticipant: ChatUserSummary | null;
    sharingLinks?: { profileUrl: string | null; cvUrl: string | null };
}>();

const messagesContainer = ref<HTMLElement | null>(null);
const messageComposerRef = ref<InstanceType<typeof MessageComposer> | null>(null);
const localConversations = ref<ChatConversation[]>([]);
const loadingConversations = ref(false);
const loadingMoreConversations = ref(false);
const hasMoreConversations = ref(false);
const loadingOlder = ref(false);
const localMessages = ref<ChatMessage[]>([]);
const hasMoreOlder = ref(false);
const replyToMessage = ref<ChatMessage | null>(null);

function syncMessagesFromProps() {
    localMessages.value = [...(props.messages?.data ?? [])];
    hasMoreOlder.value = props.messages?.has_more ?? false;
}

watch(
    () => [props.selectedConversationId, props.messages],
    () => {
        if (props.selectedConversationId) {
            syncMessagesFromProps();
        } else {
            localMessages.value = [];
            hasMoreOlder.value = false;
        }
    },
    { immediate: true },
);

const messagesList = computed(() =>
    [...localMessages.value].sort(
        (a, b) =>
            new Date(a.created_at).getTime() - new Date(b.created_at).getTime(),
    ),
);
const isSending = ref(false);
const mobileShowChat = ref(!!props.selectedConversationId);

const activeConversation = computed(() =>
    localConversations.value.find((c) => c.id === props.selectedConversationId),
);

async function fetchConversations() {
    const isInitial = localConversations.value.length === 0;
    if (isInitial) loadingConversations.value = true;
    try {
        const res = await fetch('/api/conversations', {
            credentials: 'same-origin',
        });
        if (!res.ok) return;
        const json = (await res.json()) as {
            data: ChatConversation[];
            meta?: { has_more: boolean };
        };
        localConversations.value = json.data ?? [];
        hasMoreConversations.value = json.meta?.has_more ?? false;
    } catch {
        // ignore
    } finally {
        if (isInitial) loadingConversations.value = false;
    }
}

async function loadMoreConversations() {
    if (
        loadingMoreConversations.value ||
        !hasMoreConversations.value
    ) {
        return;
    }
    const last = localConversations.value.at(-1);
    if (!last?.id) return;

    loadingMoreConversations.value = true;
    try {
        const params = new URLSearchParams({
            before_id: String(last.id),
        });
        const res = await fetch(`/api/conversations?${params}`, {
            credentials: 'same-origin',
        });
        if (!res.ok) return;
        const json = (await res.json()) as {
            data: ChatConversation[];
            meta?: { has_more: boolean };
        };
        const newItems = json.data ?? [];
        localConversations.value = [...localConversations.value, ...newItems];
        hasMoreConversations.value = json.meta?.has_more ?? false;
    } catch {
        // ignore
    } finally {
        loadingMoreConversations.value = false;
    }
}

function selectConversation(conversationId: number) {
    mobileShowChat.value = true;
    router.get(`/messages/${conversationId}`, {}, { preserveState: true });
}

function goBackToList() {
    mobileShowChat.value = false;
    router.get('/messages', {}, { preserveState: true });
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

async function loadOlderMessages() {
    if (
        !props.selectedConversationId ||
        loadingOlder.value ||
        !hasMoreOlder.value
    ) {
        return;
    }
    const oldestId = localMessages.value.reduce(
        (min, m) => (m.id < min ? m.id : min),
        Number.POSITIVE_INFINITY,
    );
    if (oldestId === Number.POSITIVE_INFINITY) {
        return;
    }

    const container = messagesContainer.value;
    const oldScrollHeight = container?.scrollHeight ?? 0;
    const oldScrollTop = container?.scrollTop ?? 0;

    loadingOlder.value = true;
    try {
        const url = `/api/conversations/${props.selectedConversationId}/messages?before_id=${oldestId}`;
        const res = await fetch(url, { credentials: 'same-origin' });
        if (!res.ok) {
            throw new Error('Failed to load messages');
        }
        const json = (await res.json()) as {
            data: ChatMessage[];
            has_more: boolean;
        };
        localMessages.value = [...(json.data ?? []), ...localMessages.value];
        hasMoreOlder.value = json.has_more ?? false;

        nextTick(() => {
            if (container) {
                const newScrollHeight = container.scrollHeight;
                container.scrollTop =
                    oldScrollTop + (newScrollHeight - oldScrollHeight);
            }
        });
    } finally {
        loadingOlder.value = false;
    }
}

function handleScroll() {
    const container = messagesContainer.value;
    if (!container || !hasMoreOlder.value || loadingOlder.value) return;
    if (container.scrollTop < 150) {
        loadOlderMessages();
    }
}

async function handleSend(payload: {
    body: string;
    attachments: File[];
    reply_to_id?: number;
}) {
    if (!props.selectedConversationId) return;

    isSending.value = true;
    replyToMessage.value = null;

    const formData = new FormData();
    if (payload.body) {
        formData.append('body', payload.body);
    }
    if (payload.reply_to_id) {
        formData.append('reply_to_id', String(payload.reply_to_id));
    }
    payload.attachments.forEach((file, i) => {
        formData.append(`attachments[${i}]`, file);
    });

    const token = document.cookie
        .match(/XSRF-TOKEN=([^;]+)/)?.[1];
    const headers: Record<string, string> = {};
    if (token) {
        headers['X-XSRF-TOKEN'] = decodeURIComponent(token);
    }
    headers['Accept'] = 'application/json';

    try {
        const res = await fetch(
            `/api/conversations/${props.selectedConversationId}/messages`,
            {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers,
            },
        );

        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(
                (err as { message?: string })?.message ?? 'Failed to send message',
            );
        }

        const json = (await res.json()) as { message: ChatMessage };
        if (json.message) {
            localMessages.value = [...localMessages.value, json.message];
            scrollToBottom();
            await fetchConversations();
        }
    } catch (e) {
        console.error('Send message failed:', e);
    } finally {
        isSending.value = false;
        nextTick(() => messageComposerRef.value?.focus());
    }
}

function setupScrollListener() {
    const container = messagesContainer.value;
    if (container && props.selectedConversationId) {
        container.removeEventListener('scroll', handleScroll);
        container.addEventListener('scroll', handleScroll, { passive: true });
    }
}

onMounted(() => {
    fetchConversations();
    scrollToBottom();
    nextTick(setupScrollListener);

    if (props.selectedConversationId) {
        startPolling();
    }
});

watch(
    () => props.selectedConversationId,
    (id) => {
        if (id) {
            nextTick(setupScrollListener);
        }
    },
);

onBeforeUnmount(() => {
    messagesContainer.value?.removeEventListener('scroll', handleScroll);
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
            fetchConversations().then(() => {
                const container = messagesContainer.value;
                if (!container) return;
                const { scrollTop, scrollHeight, clientHeight } = container;
                const isNearBottom =
                    scrollHeight - scrollTop - clientHeight < 100;
                if (isNearBottom) {
                    scrollToBottom();
                }
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
    localConversations.value.reduce((sum, c) => sum + c.unread_count, 0),
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
                class="min-h-0 w-full flex-col border-r md:flex md:w-80 lg:w-96"
                :class="
                    mobileShowChat && selectedConversationId ? 'hidden' : 'flex'
                "
            >
                <div class="border-b px-4 py-3">
                    <div class="flex items-center justify-between">
                        <h1 class="text-lg font-semibold">Messages</h1>
                        <NewConversationDialog />
                    </div>
                    <div
                        class="mt-3 flex gap-2.5 rounded-lg border border-border/80 bg-muted/40 px-3 py-2.5"
                        role="note"
                        aria-label="Chat safety guidelines"
                    >
                        <ShieldCheck
                            class="mt-0.5 size-4 shrink-0 text-primary/80"
                            aria-hidden
                        />
                        <p
                            class="text-xs leading-relaxed text-muted-foreground"
                        >
                            Don't share sensitive information. Chat is for Find
                            Developer platform topics only. For secrets, use
                            <a
                                href="https://onetimesecret.com/en/"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center font-medium text-primary underline-offset-4 transition-colors hover:text-primary/90 hover:underline focus-visible:rounded focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                            >
                                OneTimeSecret
                            </a>
                            .
                        </p>
                    </div>
                </div>
                <ConversationList
                    :conversations="localConversations"
                    :active-conversation-id="selectedConversationId"
                    :loading="loadingConversations"
                    :has-more="hasMoreConversations"
                    :loading-more="loadingMoreConversations"
                    @select="selectConversation"
                    @load-more="loadMoreConversations"
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
                            <div class="flex items-center gap-2">
                                <Link
                                    v-if="selectedParticipant.developer_slug"
                                    :href="`/developers/${selectedParticipant.developer_slug}`"
                                    class="text-sm font-medium text-foreground underline-offset-4 hover:underline"
                                >
                                    {{ selectedParticipant.name }}
                                </Link>
                                <p v-else class="text-sm font-medium">
                                    {{ selectedParticipant.name }}
                                </p>
                                <span
                                    v-if="selectedParticipant.user_type_label"
                                    class="rounded-full bg-muted px-2 py-0.5 text-[10px] font-medium text-muted-foreground"
                                >
                                    {{ selectedParticipant.user_type_label }}
                                </span>
                            </div>
                            <p class="text-xs text-muted-foreground">
                                {{ selectedParticipant.email }}
                            </p>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div
                        ref="messagesContainer"
                        class="flex-1 overflow-y-auto px-4 py-4"
                    >
                        <div class="flex flex-col space-y-4">
                            <div
                                v-if="hasMoreOlder"
                                class="flex min-h-[48px] flex-col items-center justify-center py-3"
                            >
                                <button
                                    v-if="!loadingOlder"
                                    type="button"
                                    class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                                    @click="loadOlderMessages"
                                >
                                    Load older messages
                                </button>
                                <span
                                    v-else
                                    class="text-sm text-muted-foreground"
                                >
                                    Loading older messages...
                                </span>
                            </div>
                            <div
                                v-if="messagesList.length === 0"
                                class="flex h-full min-h-[200px] items-center justify-center"
                            >
                                <p class="text-sm text-muted-foreground">
                                    No messages yet. Start the conversation!
                                </p>
                            </div>
                            <MessageBubble
                                v-for="message in messagesList"
                                :key="message.id"
                                :message="message"
                                @reply="replyToMessage = $event"
                            />
                        </div>
                    </div>

                    <!-- Composer -->
                    <MessageComposer
                        ref="messageComposerRef"
                        :disabled="isSending"
                        :profile-url="sharingLinks?.profileUrl ?? null"
                        :cv-url="sharingLinks?.cvUrl ?? null"
                        :reply-to="replyToMessage"
                        @send="handleSend"
                        @clear-reply="replyToMessage = null"
                    />
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
