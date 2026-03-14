<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { refDebounced } from '@vueuse/core';
import { ExternalLink, MessageCircle, Search } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import Pagination from '@/components/Pagination.vue';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

type ConversationRow = {
    id: number;
    participants: { id: number; name: string; email: string }[];
    messages_count: number;
    last_message: {
        body: string | null;
        user_name: string | null;
        created_at: string;
    } | null;
    created_at: string;
    updated_at: string;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatedConversations = {
    data: ConversationRow[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
};

type Props = {
    conversations: PaginatedConversations;
    filters?: { search?: string };
};

const props = withDefaults(defineProps<Props>(), {
    filters: () => ({}),
});

const page = usePage();
const searchQuery = ref(props.filters?.search ?? '');
const debouncedSearch = refDebounced(searchQuery, 300);

const flash = computed(
    () => page.props.flash as { success?: string; error?: string } | undefined,
);

function buildFilters(): Record<string, string | undefined> {
    return {
        search: searchQuery.value || undefined,
    };
}

watch(debouncedSearch, () => {
    router.get('/dashboard/conversations', buildFilters(), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});

function formatDate(iso: string): string {
    const d = new Date(iso);
    return d.toLocaleString(undefined, {
        dateStyle: 'short',
        timeStyle: 'short',
    });
}

function participantsDisplay(
    participants: ConversationRow['participants'],
): string {
    return participants.map((p) => p.name).join(' & ') || '—';
}

function lastMessagePreview(
    lastMessage: ConversationRow['last_message'],
): string {
    if (!lastMessage?.body) return '—';
    const text = lastMessage.body.replace(/<[^>]*>/g, '').trim();
    const truncated = text.length > 80 ? `${text.slice(0, 80)}…` : text;
    const sender = lastMessage.user_name ? ` (${lastMessage.user_name})` : '';
    return truncated ? `${truncated}${sender}` : '—';
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Conversations', href: '/dashboard/conversations' },
];
</script>

<template>
    <Head title="Conversations" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div
                v-if="flash?.success"
                class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:border-green-800 dark:bg-green-950/50 dark:text-green-200"
            >
                {{ flash.success }}
            </div>
            <div
                v-if="flash?.error"
                class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 dark:border-red-800 dark:bg-red-950/50 dark:text-red-200"
            >
                {{ flash.error }}
            </div>

            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Conversations
                    </h1>
                    <p class="text-muted-foreground">
                        All chat conversations (super admin only)
                    </p>
                </div>
            </div>

            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative flex-1 sm:max-w-sm">
                        <Search
                            class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                        />
                        <Input
                            v-model="searchQuery"
                            type="search"
                            placeholder="Search conversations..."
                            class="pl-9"
                        />
                    </div>
                </div>
                <p
                    v-if="conversations.total > 0"
                    class="text-sm text-muted-foreground"
                >
                    Showing {{ conversations.from }}–{{ conversations.to }} of
                    {{ conversations.total }}
                </p>
            </div>

            <div v-if="conversations.data.length > 0" class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[80px]">ID</TableHead>
                            <TableHead class="w-[200px]"
                                >Participants</TableHead
                            >
                            <TableHead class="w-[100px]">Messages</TableHead>
                            <TableHead>Last Message</TableHead>
                            <TableHead class="w-[160px]"
                                >Last Activity</TableHead
                            >
                            <TableHead class="w-[80px]" />
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="conv in conversations.data"
                            :key="conv.id"
                        >
                            <TableCell class="text-sm text-muted-foreground">
                                {{ conv.id }}
                            </TableCell>
                            <TableCell class="text-sm">
                                {{ participantsDisplay(conv.participants) }}
                            </TableCell>
                            <TableCell class="text-sm text-muted-foreground">
                                {{ conv.messages_count }}
                            </TableCell>
                            <TableCell class="max-w-[300px] truncate text-sm">
                                {{ lastMessagePreview(conv.last_message) }}
                            </TableCell>
                            <TableCell
                                class="text-sm whitespace-nowrap text-muted-foreground"
                            >
                                {{ formatDate(conv.updated_at) }}
                            </TableCell>
                            <TableCell>
                                <Link
                                    :href="`/dashboard/conversations/${conv.id}`"
                                    class="inline-flex items-center gap-1 text-sm text-primary hover:underline"
                                >
                                    View
                                    <ExternalLink class="size-3.5" />
                                </Link>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center rounded-xl border border-dashed py-12"
            >
                <MessageCircle class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No conversations yet</h3>
                <p class="text-center text-sm text-muted-foreground">
                    {{
                        filters?.search
                            ? 'No conversations match your search.'
                            : 'Conversations will appear here when users start chatting.'
                    }}
                </p>
            </div>

            <Pagination
                v-if="conversations.links?.length"
                :links="conversations.links"
            />
        </div>
    </AppLayout>
</template>
