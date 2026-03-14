<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Calendar,
    Download,
    FileText,
    MessageCircle,
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

type AttachmentDetail = {
    id: number;
    message_id: number;
    file_name: string;
    file_path: string;
    file_type: string;
    file_size: number;
    file_url: string | null;
    sender: { id: number; name: string; email: string } | null;
    conversation_participants: {
        id: number;
        name: string;
        email: string;
    }[];
    created_at: string;
};

type Props = {
    attachment: AttachmentDetail;
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
        return `${bytes} bytes`;
    }
    if (bytes < 1024 * 1024) {
        return `${(bytes / 1024).toFixed(1)} KB`;
    }
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

const isImage = () =>
    props.attachment.file_type.toLowerCase().startsWith('image/');

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Attachments', href: '/dashboard/message-attachments' },
    { title: `#${props.attachment.id}`, href: '#' },
];
</script>

<template>
    <Head :title="`Attachment: ${attachment.file_name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full space-y-6 rounded-xl p-4">
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10"
                    >
                        <Paperclip class="h-6 w-6 text-primary" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight">
                            {{ attachment.file_name }}
                        </h1>
                        <p class="text-muted-foreground">
                            Attachment #{{ attachment.id }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Button
                        v-if="attachment.file_url"
                        variant="outline"
                        as-child
                    >
                        <a
                            :href="attachment.file_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            download
                        >
                            <Download class="mr-2 h-4 w-4" />
                            Download
                        </a>
                    </Button>
                    <Button variant="outline" as-child>
                        <Link href="/dashboard/message-attachments">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Back to attachments
                        </Link>
                    </Button>
                </div>
            </div>

            <div
                v-if="isImage() && attachment.file_url"
                class="rounded-lg border"
            >
                <img
                    :src="attachment.file_url"
                    :alt="attachment.file_name"
                    class="max-h-96 w-full object-contain"
                />
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader class="pb-2">
                        <h3
                            class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                        >
                            <FileText class="size-4" />
                            File Info
                        </h3>
                    </CardHeader>
                    <CardContent class="space-y-2 text-sm">
                        <p>
                            <span class="text-muted-foreground">Name:</span>
                            {{ attachment.file_name }}
                        </p>
                        <p>
                            <span class="text-muted-foreground">Type:</span>
                            <code
                                class="rounded bg-muted px-1.5 py-0.5 font-mono text-xs"
                            >
                                {{ attachment.file_type }}
                            </code>
                        </p>
                        <p>
                            <span class="text-muted-foreground">Size:</span>
                            {{ formatFileSize(attachment.file_size) }}
                        </p>
                        <p>
                            <span class="text-muted-foreground">Path:</span>
                            <code
                                class="block rounded bg-muted px-1.5 py-0.5 font-mono text-xs break-all"
                            >
                                {{ attachment.file_path }}
                            </code>
                        </p>
                    </CardContent>
                </Card>

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
                        <p v-if="attachment.sender">
                            <span class="text-muted-foreground">Name:</span>
                            {{ attachment.sender.name }}
                        </p>
                        <p v-if="attachment.sender?.email">
                            <span class="text-muted-foreground">Email:</span>
                            {{ attachment.sender.email }}
                        </p>
                        <p
                            v-if="!attachment.sender"
                            class="text-muted-foreground"
                        >
                            —
                        </p>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader class="pb-2">
                        <h3
                            class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                        >
                            <MessageCircle class="size-4" />
                            Message
                        </h3>
                    </CardHeader>
                    <CardContent>
                        <Link
                            :href="`/dashboard/chat-messages/${attachment.message_id}`"
                            class="inline-flex items-center gap-2 text-sm text-primary hover:underline"
                        >
                            View message #{{ attachment.message_id }}
                            <MessageCircle class="size-4" />
                        </Link>
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
                    <CardContent class="text-sm">
                        <p>
                            <span class="text-muted-foreground">Created:</span>
                            {{ formatDate(attachment.created_at) }}
                        </p>
                    </CardContent>
                </Card>
            </div>

            <Card v-if="attachment.conversation_participants.length > 0">
                <CardHeader class="pb-2">
                    <h3
                        class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                    >
                        <Users class="size-4" />
                        Conversation Participants
                    </h3>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-wrap gap-2">
                        <Badge
                            v-for="participant in attachment.conversation_participants"
                            :key="participant.id"
                            variant="secondary"
                            class="font-normal"
                        >
                            {{ participant.name }}
                            <span class="ml-1 text-muted-foreground">
                                ({{ participant.email }})
                            </span>
                        </Badge>
                    </div>
                </CardContent>
            </Card>

            <Card v-else>
                <CardContent
                    class="py-8 text-center text-sm text-muted-foreground"
                >
                    No conversation participants recorded.
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
