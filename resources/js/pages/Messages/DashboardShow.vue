<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Calendar,
    Download,
    MessageSquare,
    Paperclip,
    User,
    Users,
} from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

type MessageDetail = {
    id: number;
    conversation_id: number;
    user: { id: number; name: string; email: string } | null;
    body: string | null;
    conversation_participants: { id: number; name: string; email: string }[];
    attachments: {
        id: number;
        file_name: string;
        file_path: string;
        file_type: string;
        file_size: number;
        file_url: string | null;
        created_at: string;
    }[];
    created_at: string;
};

type Props = {
    message: MessageDetail;
};

const props = defineProps<Props>();

function formatDate(iso: string): string {
    const d = new Date(iso);
    return d.toLocaleString(undefined, {
        dateStyle: 'medium',
        timeStyle: 'medium',
    });
}

function formatFileSize(bytes: number): string {
    if (bytes < 1024) {
        return `${bytes} B`;
    }
    if (bytes < 1024 * 1024) {
        return `${(bytes / 1024).toFixed(1)} KB`;
    }
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Messages', href: '/dashboard/chat-messages' },
    { title: `#${props.message.id}`, href: '#' },
];
</script>

<template>
    <Head :title="`Message #${message.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full space-y-6 rounded-xl p-4">
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10"
                    >
                        <MessageSquare class="h-6 w-6 text-primary" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight">
                            Message #{{ message.id }}
                        </h1>
                        <p class="text-muted-foreground">
                            Chat message details
                        </p>
                    </div>
                </div>
                <Button variant="outline" as-child>
                    <Link href="/dashboard/chat-messages">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Back to messages
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
                            Sender
                        </h3>
                    </CardHeader>
                    <CardContent class="space-y-2 text-sm">
                        <p v-if="message.user">
                            <span class="text-muted-foreground">Name:</span>
                            {{ message.user.name }}
                        </p>
                        <p v-if="message.user">
                            <span class="text-muted-foreground">Email:</span>
                            {{ message.user.email }}
                        </p>
                        <p v-if="!message.user" class="text-muted-foreground">
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
                            Timestamp
                        </h3>
                    </CardHeader>
                    <CardContent class="space-y-2 text-sm">
                        <p>
                            <span class="text-muted-foreground">Created:</span>
                            {{ formatDate(message.created_at) }}
                        </p>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader class="pb-2">
                    <h3
                        class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                    >
                        Conversation
                    </h3>
                </CardHeader>
                <CardContent>
                    <Link
                        :href="`/dashboard/conversations/${message.conversation_id}`"
                        class="text-primary hover:underline"
                    >
                        View conversation #{{ message.conversation_id }}
                    </Link>
                </CardContent>
            </Card>

            <Card v-if="message.conversation_participants.length > 0">
                <CardHeader class="pb-2">
                    <h3
                        class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                    >
                        <Users class="size-4" />
                        Conversation Participants
                    </h3>
                </CardHeader>
                <CardContent>
                    <ul class="space-y-2 text-sm">
                        <li
                            v-for="participant in message.conversation_participants"
                            :key="participant.id"
                        >
                            {{ participant.name }}
                            <span class="text-muted-foreground">
                                ({{ participant.email }})
                            </span>
                        </li>
                    </ul>
                </CardContent>
            </Card>

            <Card v-if="message.body">
                <CardHeader class="pb-2">
                    <h3 class="text-sm font-medium text-muted-foreground">
                        Message Body
                    </h3>
                </CardHeader>
                <CardContent>
                    <div
                        class="prose prose-sm dark:prose-invert max-w-none"
                        v-html="message.body"
                    />
                </CardContent>
            </Card>

            <Card v-else>
                <CardContent
                    class="py-8 text-center text-sm text-muted-foreground"
                >
                    No message body.
                </CardContent>
            </Card>

            <Card v-if="message.attachments.length > 0">
                <CardHeader class="pb-2">
                    <h3
                        class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                    >
                        <Paperclip class="size-4" />
                        Attachments
                    </h3>
                </CardHeader>
                <CardContent>
                    <ul class="space-y-3">
                        <li
                            v-for="attachment in message.attachments"
                            :key="attachment.id"
                            class="flex flex-wrap items-center justify-between gap-2 rounded-lg border p-3"
                        >
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium">
                                    {{ attachment.file_name }}
                                </p>
                                <div
                                    class="flex items-center gap-2 text-sm text-muted-foreground"
                                >
                                    <Badge variant="secondary" class="text-xs">
                                        {{ attachment.file_type }}
                                    </Badge>
                                    <span>{{
                                        formatFileSize(attachment.file_size)
                                    }}</span>
                                </div>
                            </div>
                            <a
                                v-if="attachment.file_url"
                                :href="attachment.file_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 text-sm text-primary hover:underline"
                            >
                                <Download class="size-4" />
                                Download
                            </a>
                            <span v-else class="text-sm text-muted-foreground">
                                No download URL
                            </span>
                        </li>
                    </ul>
                </CardContent>
            </Card>

            <Card v-else>
                <CardContent
                    class="py-8 text-center text-sm text-muted-foreground"
                >
                    No attachments.
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
