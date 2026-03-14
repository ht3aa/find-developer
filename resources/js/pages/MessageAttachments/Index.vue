<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { refDebounced } from '@vueuse/core';
import { ExternalLink, Paperclip, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
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

type AttachmentRow = {
    id: number;
    message_id: number;
    file_name: string;
    file_type: string;
    file_size: number;
    file_url: string | null;
    sender: { id: number; name: string; email: string } | null;
    created_at: string;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatedAttachments = {
    data: AttachmentRow[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
};

type Props = {
    attachments: PaginatedAttachments;
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
    router.get('/dashboard/message-attachments', buildFilters(), {
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

function formatFileSize(bytes: number): string {
    if (bytes < 1024) {
        return `${bytes} bytes`;
    }
    if (bytes < 1024 * 1024) {
        return `${(bytes / 1024).toFixed(1)} KB`;
    }
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Attachments', href: '/dashboard/message-attachments' },
];
</script>

<template>
    <Head title="Message Attachments" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Message Attachments
                    </h1>
                    <p class="text-muted-foreground">
                        All chat file attachments (super admin only)
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
                            placeholder="Search file name, type, sender..."
                            class="pl-9"
                        />
                    </div>
                </div>
                <p
                    v-if="attachments.total > 0"
                    class="text-sm text-muted-foreground"
                >
                    Showing {{ attachments.from }}–{{ attachments.to }} of
                    {{ attachments.total }}
                </p>
            </div>

            <div v-if="attachments.data.length > 0" class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[80px]">ID</TableHead>
                            <TableHead class="min-w-[160px]"
                                >File Name</TableHead
                            >
                            <TableHead class="w-[100px]">Type</TableHead>
                            <TableHead class="w-[100px]">Size</TableHead>
                            <TableHead class="w-[160px]">Sender</TableHead>
                            <TableHead class="w-[100px]">Message ID</TableHead>
                            <TableHead class="w-[140px]">Date</TableHead>
                            <TableHead class="w-[100px]" />
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="attachment in attachments.data"
                            :key="attachment.id"
                        >
                            <TableCell class="text-sm text-muted-foreground">
                                {{ attachment.id }}
                            </TableCell>
                            <TableCell class="font-medium">
                                {{ attachment.file_name }}
                            </TableCell>
                            <TableCell class="text-sm text-muted-foreground">
                                {{ attachment.file_type }}
                            </TableCell>
                            <TableCell class="text-sm text-muted-foreground">
                                {{ formatFileSize(attachment.file_size) }}
                            </TableCell>
                            <TableCell class="text-sm text-muted-foreground">
                                {{ attachment.sender?.name ?? '—' }}
                            </TableCell>
                            <TableCell>
                                <Link
                                    :href="`/dashboard/chat-messages/${attachment.message_id}`"
                                    class="inline-flex items-center gap-1 text-sm text-primary hover:underline"
                                >
                                    {{ attachment.message_id }}
                                    <ExternalLink class="size-3.5" />
                                </Link>
                            </TableCell>
                            <TableCell
                                class="text-sm whitespace-nowrap text-muted-foreground"
                            >
                                {{ formatDate(attachment.created_at) }}
                            </TableCell>
                            <TableCell>
                                <Link
                                    :href="`/dashboard/message-attachments/${attachment.id}`"
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
                <Paperclip class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No attachments yet</h3>
                <p class="text-center text-sm text-muted-foreground">
                    {{
                        filters?.search
                            ? 'No attachments match your search.'
                            : 'File attachments from chat messages will appear here.'
                    }}
                </p>
            </div>

            <Pagination
                v-if="attachments.links?.length"
                :links="attachments.links"
            />
        </div>
    </AppLayout>
</template>
