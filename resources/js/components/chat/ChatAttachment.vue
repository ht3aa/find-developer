<script setup lang="ts">
import { Download, FileText } from 'lucide-vue-next';
import { formatFileSize, isImageType } from '@/composables/useChat';
import type { MessageAttachment } from '@/types';

defineProps<{
    attachment: MessageAttachment;
}>();
</script>

<template>
    <div class="overflow-hidden rounded-lg border bg-background">
        <a
            v-if="isImageType(attachment.file_type)"
            :href="attachment.file_url"
            target="_blank"
            rel="noopener noreferrer"
            class="block"
        >
            <img
                :src="attachment.file_url"
                :alt="attachment.file_name"
                class="max-h-48 w-full object-cover transition-opacity hover:opacity-90"
                loading="lazy"
            />
        </a>

        <a
            v-else
            :href="attachment.file_url"
            target="_blank"
            rel="noopener noreferrer"
            class="flex items-center gap-3 p-3 transition-colors hover:bg-accent"
        >
            <div
                class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-muted"
            >
                <FileText class="size-5 text-muted-foreground" />
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium">
                    {{ attachment.file_name }}
                </p>
                <p class="text-xs text-muted-foreground">
                    {{ formatFileSize(attachment.file_size) }}
                </p>
            </div>
            <Download class="size-4 shrink-0 text-muted-foreground" />
        </a>
    </div>
</template>
