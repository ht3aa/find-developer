<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Calendar, MessageCircle, User } from 'lucide-vue-next';
import { computed } from 'vue';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
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

type ConversationDetail = {
    id: number;
    participants: { id: number; name: string; email: string }[];
    created_at: string;
    updated_at: string;
};

type MessageRow = {
    id: number;
    user: { id: number; name: string; email: string } | null;
    body: string | null;
    attachments_count: number;
    attachments: {
        id: number;
        file_name: string;
        file_type: string;
        file_size: number;
    }[];
    created_at: string;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatedMessages = {
    data: MessageRow[];
    links: PaginationLink[];
    total: number;
    from: number | null;
    to: number | null;
};

type Props = {
    conversation: ConversationDetail;
    messages: PaginatedMessages;
};

const props = defineProps<Props>();

const page = usePage();
const flash = computed(
    () => page.props.flash as { success?: string; error?: string } | undefined,
);

function formatDate(iso: string): string {
    const d = new Date(iso);
    return d.toLocaleString(undefined, {
        dateStyle: 'medium',
        timeStyle: 'medium',
    });
}

function bodyPreview(body: string | null): string {
    if (!body) return '—';
    const text = body.replace(/<[^>]*>/g, '').trim();
    return text.length > 200 ? `${text.slice(0, 200)}…` : text || '—';
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Conversations', href: '/dashboard/conversations' },
    { title: `#${props.conversation.id}`, href: '#' },
];
</script>

<template>
    <Head :title="`Conversation #${conversation.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full space-y-6 rounded-xl p-4">
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
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10"
                    >
                        <MessageCircle class="h-6 w-6 text-primary" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight">
                            Conversation #{{ conversation.id }}
                        </h1>
                        <p class="text-muted-foreground">
                            Chat messages between participants
                        </p>
                    </div>
                </div>
                <Button variant="outline" as-child>
                    <Link href="/dashboard/conversations">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Back to conversations
                    </Link>
                </Button>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader class="pb-2">
                        <h3
                            class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                        >
                            <User class="size-4" />
                            Participants
                        </h3>
                    </CardHeader>
                    <CardContent class="space-y-2 text-sm">
                        <div
                            v-for="p in conversation.participants"
                            :key="p.id"
                            class="space-y-0.5"
                        >
                            <p class="font-medium">{{ p.name }}</p>
                            <p class="text-muted-foreground">{{ p.email }}</p>
                        </div>
                        <p
                            v-if="conversation.participants.length === 0"
                            class="text-muted-foreground"
                        >
                            —
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <h3
                            class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                        >
                            <Calendar class="size-4" />
                            Timestamps
                        </h3>
                    </CardHeader>
                    <CardContent class="space-y-2 text-sm">
                        <p>
                            <span class="text-muted-foreground">Created:</span>
                            {{ formatDate(conversation.created_at) }}
                        </p>
                        <p>
                            <span class="text-muted-foreground">Updated:</span>
                            {{ formatDate(conversation.updated_at) }}
                        </p>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader class="pb-2">
                    <h3 class="text-sm font-medium text-muted-foreground">
                        Messages
                    </h3>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="messages.data.length > 0"
                        class="rounded-md border"
                    >
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[180px]"
                                        >Sender</TableHead
                                    >
                                    <TableHead>Body</TableHead>
                                    <TableHead class="w-[100px]">
                                        Attachments
                                    </TableHead>
                                    <TableHead class="w-[160px]"
                                        >Date</TableHead
                                    >
                                    <TableHead class="w-[80px]" />
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-for="msg in messages.data"
                                    :key="msg.id"
                                >
                                    <TableCell class="text-sm">
                                        <div v-if="msg.user">
                                            <p class="font-medium">
                                                {{ msg.user.name }}
                                            </p>
                                            <p
                                                class="text-xs text-muted-foreground"
                                            >
                                                {{ msg.user.email }}
                                            </p>
                                        </div>
                                        <span
                                            v-else
                                            class="text-muted-foreground"
                                            >—</span
                                        >
                                    </TableCell>
                                    <TableCell class="max-w-[400px]">
                                        <div
                                            class="line-clamp-2 text-sm text-muted-foreground"
                                        >
                                            {{ bodyPreview(msg.body) }}
                                        </div>
                                    </TableCell>
                                    <TableCell
                                        class="text-sm text-muted-foreground"
                                    >
                                        {{ msg.attachments_count }}
                                    </TableCell>
                                    <TableCell
                                        class="text-sm whitespace-nowrap text-muted-foreground"
                                    >
                                        {{ formatDate(msg.created_at) }}
                                    </TableCell>
                                    <TableCell>
                                        <Link
                                            :href="`/dashboard/chat-messages/${msg.id}`"
                                            class="inline-flex items-center gap-1 text-sm text-primary hover:underline"
                                        >
                                            View
                                        </Link>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <p
                        v-else
                        class="py-8 text-center text-sm text-muted-foreground"
                    >
                        No messages in this conversation.
                    </p>

                    <div v-if="messages.links?.length" class="mt-4">
                        <Pagination :links="messages.links" />
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
