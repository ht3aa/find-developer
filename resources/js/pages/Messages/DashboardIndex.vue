<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { refDebounced } from '@vueuse/core';
import { ExternalLink, MessageSquare, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import Pagination from '@/components/Pagination.vue';
import { Badge } from '@/components/ui/badge';
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

type MessageRow = {
    id: number;
    conversation_id: number;
    user: { id: number; name: string; email: string } | null;
    body: string | null;
    body_preview: string | null;
    attachments_count: number;
    created_at: string;
};

type PaginatedMessages = {
    data: MessageRow[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
};

type Props = {
    messages: PaginatedMessages;
    filters?: { search?: string };
};

const props = withDefaults(defineProps<Props>(), {
    filters: () => ({}),
});

const searchQuery = ref(props.filters?.search ?? '');
const debouncedSearch = refDebounced(searchQuery, 300);

function buildFilters(): Record<string, string | undefined> {
    return {
        search: searchQuery.value || undefined,
    };
}

watch(debouncedSearch, () => {
    router.get('/dashboard/chat-messages', buildFilters(), {
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

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Messages', href: '/dashboard/chat-messages' },
];
</script>

<template>
    <Head title="Messages" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Messages
                    </h1>
                    <p class="text-muted-foreground">
                        All chat messages (super admin only)
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
                            placeholder="Search messages..."
                            class="pl-9"
                        />
                    </div>
                </div>
                <p
                    v-if="messages.total > 0"
                    class="text-sm text-muted-foreground"
                >
                    Showing {{ messages.from }}–{{ messages.to }} of
                    {{ messages.total }}
                </p>
            </div>

            <div v-if="messages.data.length > 0" class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[80px]">ID</TableHead>
                            <TableHead class="w-[120px]"
                                >Conversation</TableHead
                            >
                            <TableHead class="w-[200px]">Sender</TableHead>
                            <TableHead>Body Preview</TableHead>
                            <TableHead class="w-[100px]">Attachments</TableHead>
                            <TableHead class="w-[140px]">Date</TableHead>
                            <TableHead class="w-[80px]" />
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="message in messages.data"
                            :key="message.id"
                        >
                            <TableCell class="text-sm text-muted-foreground">
                                {{ message.id }}
                            </TableCell>
                            <TableCell>
                                <Link
                                    :href="`/dashboard/conversations/${message.conversation_id}`"
                                    class="text-primary hover:underline"
                                >
                                    #{{ message.conversation_id }}
                                </Link>
                            </TableCell>
                            <TableCell class="text-sm text-muted-foreground">
                                <span v-if="message.user">
                                    {{ message.user.name }}
                                    <span class="block truncate text-xs">
                                        {{ message.user.email }}
                                    </span>
                                </span>
                                <span v-else>—</span>
                            </TableCell>
                            <TableCell
                                class="max-w-[200px] truncate text-sm text-muted-foreground"
                            >
                                {{
                                    message.body_preview ?? message.body ?? '—'
                                }}
                            </TableCell>
                            <TableCell>
                                <Badge
                                    v-if="message.attachments_count > 0"
                                    variant="secondary"
                                >
                                    {{ message.attachments_count }}
                                </Badge>
                                <span v-else class="text-muted-foreground"
                                    >0</span
                                >
                            </TableCell>
                            <TableCell
                                class="text-sm whitespace-nowrap text-muted-foreground"
                            >
                                {{ formatDate(message.created_at) }}
                            </TableCell>
                            <TableCell>
                                <Link
                                    :href="`/dashboard/chat-messages/${message.id}`"
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
                <MessageSquare class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No messages yet</h3>
                <p class="text-center text-sm text-muted-foreground">
                    {{
                        filters?.search
                            ? 'No messages match your search.'
                            : 'Messages will appear here as users chat.'
                    }}
                </p>
            </div>

            <Pagination v-if="messages.links?.length" :links="messages.links" />
        </div>
    </AppLayout>
</template>
