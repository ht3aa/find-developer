<script setup lang="ts">
import Link from '@tiptap/extension-link';
import { Placeholder } from '@tiptap/extension-placeholder';
import StarterKit from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import {
    Bold,
    Code,
    FileText,
    Italic,
    List,
    ListOrdered,
    Paperclip,
    SendHorizontal,
    UserCircle,
    X,
} from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';

import type { ChatMessage } from '@/types';

const props = defineProps<{
    disabled?: boolean;
    profileUrl?: string | null;
    cvUrl?: string | null;
    replyTo?: ChatMessage | null;
}>();

const emit = defineEmits<{
    send: [
        payload: { body: string; attachments: File[]; reply_to_id?: number },
    ];
    clearReply: [];
}>();

const attachments = ref<File[]>([]);
const fileInput = ref<HTMLInputElement | null>(null);

const editor = useEditor({
    content: '',
    extensions: [
        StarterKit,
        Link.configure({
            openOnClick: false,
            HTMLAttributes: { class: 'text-primary underline' },
        }),
        Placeholder.configure({ placeholder: 'Type a message...' }),
    ],
    editorProps: {
        handleKeyDown(view, event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                handleSend();
                return true;
            }
            return false;
        },
        attributes: {
            class: 'outline-none min-h-[40px] max-h-[120px] overflow-y-auto px-3 py-2 text-sm text-start',
            dir: 'auto',
        },
    },
});

watch(
    () => props.disabled,
    (disabled) => editor.value?.setEditable(!disabled),
);

function handleSend() {
    const body = editor.value?.getHTML() ?? '';
    const isEmpty = editor.value?.isEmpty;

    if (isEmpty && attachments.value.length === 0) return;

    emit('send', {
        body: isEmpty ? '' : body,
        attachments: [...attachments.value],
        reply_to_id: props.replyTo?.id,
    });

    editor.value?.commands.clearContent();
    attachments.value = [];
    emit('clearReply');
}

function onFileSelect(event: Event) {
    const target = event.target as HTMLInputElement;
    if (!target.files) return;
    const newFiles = Array.from(target.files);
    attachments.value = [...attachments.value, ...newFiles].slice(0, 5);
    target.value = '';
}

function removeAttachment(index: number) {
    attachments.value.splice(index, 1);
}

function insertLink(url: string, label: string) {
    if (!editor.value) return;
    editor.value
        .chain()
        .focus()
        .insertContent(`<a href="${url}" target="_blank">${label}</a>`)
        .run();
}
</script>

<template>
    <div class="border-t bg-background">
        <div
            v-if="replyTo"
            class="flex items-center justify-between gap-2 border-b bg-muted/30 px-4 py-2"
        >
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-muted-foreground">
                    Replying to {{ replyTo.user.name }}
                </p>
                <div
                    v-if="replyTo.body"
                    class="prose prose-sm dark:prose-invert mt-0.5 max-w-none text-sm [&_a]:text-primary [&_a]:underline [&_ol]:my-1 [&_p]:my-0 [&_ul]:my-1"
                    v-html="replyTo.body"
                />
                <p v-else class="text-sm text-muted-foreground">—</p>
            </div>
            <Button
                type="button"
                variant="ghost"
                size="icon"
                class="h-7 w-7 shrink-0"
                @click="emit('clearReply')"
            >
                <X class="size-4" />
            </Button>
        </div>
        <div
            v-if="attachments.length > 0"
            class="flex flex-wrap gap-2 border-b px-4 py-2"
        >
            <div
                v-for="(file, index) in attachments"
                :key="index"
                class="flex items-center gap-1.5 rounded-md bg-muted px-2.5 py-1 text-xs"
            >
                <Paperclip class="size-3 text-muted-foreground" />
                <span class="max-w-[120px] truncate">{{ file.name }}</span>
                <button
                    type="button"
                    class="rounded-full p-0.5 hover:bg-destructive/10 hover:text-destructive"
                    @click="removeAttachment(index)"
                >
                    <X class="size-3" />
                </button>
            </div>
        </div>

        <div v-if="editor" class="flex items-center gap-0.5 border-b px-2 py-1">
            <Button
                type="button"
                variant="ghost"
                size="icon"
                class="h-7 w-7"
                :class="{ 'bg-muted': editor.isActive('bold') }"
                :disabled="disabled"
                @click="editor.chain().focus().toggleBold().run()"
            >
                <Bold class="size-3.5" />
            </Button>
            <Button
                type="button"
                variant="ghost"
                size="icon"
                class="h-7 w-7"
                :class="{ 'bg-muted': editor.isActive('italic') }"
                :disabled="disabled"
                @click="editor.chain().focus().toggleItalic().run()"
            >
                <Italic class="size-3.5" />
            </Button>
            <Button
                type="button"
                variant="ghost"
                size="icon"
                class="h-7 w-7"
                :class="{ 'bg-muted': editor.isActive('bulletList') }"
                :disabled="disabled"
                @click="editor.chain().focus().toggleBulletList().run()"
            >
                <List class="size-3.5" />
            </Button>
            <Button
                type="button"
                variant="ghost"
                size="icon"
                class="h-7 w-7"
                :class="{ 'bg-muted': editor.isActive('orderedList') }"
                :disabled="disabled"
                @click="editor.chain().focus().toggleOrderedList().run()"
            >
                <ListOrdered class="size-3.5" />
            </Button>
            <Button
                type="button"
                variant="ghost"
                size="icon"
                class="h-7 w-7"
                :class="{ 'bg-muted': editor.isActive('code') }"
                :disabled="disabled"
                @click="editor.chain().focus().toggleCode().run()"
            >
                <Code class="size-3.5" />
            </Button>

            <template v-if="profileUrl || cvUrl">
                <div class="mx-1 h-4 w-px bg-border" />
                <TooltipProvider>
                    <Tooltip v-if="profileUrl">
                        <TooltipTrigger as-child>
                            <Button
                                type="button"
                                variant="ghost"
                                size="icon"
                                class="h-7 w-7"
                                :disabled="disabled"
                                @click="
                                    insertLink(profileUrl ?? '', 'My Profile')
                                "
                            >
                                <UserCircle class="size-3.5" />
                            </Button>
                        </TooltipTrigger>
                        <TooltipContent>Share my profile link</TooltipContent>
                    </Tooltip>
                    <Tooltip v-if="cvUrl">
                        <TooltipTrigger as-child>
                            <Button
                                type="button"
                                variant="ghost"
                                size="icon"
                                class="h-7 w-7"
                                :disabled="disabled"
                                @click="insertLink(cvUrl ?? '', 'My CV')"
                            >
                                <FileText class="size-3.5" />
                            </Button>
                        </TooltipTrigger>
                        <TooltipContent>Share my CV link</TooltipContent>
                    </Tooltip>
                </TooltipProvider>
            </template>
        </div>

        <div class="flex items-end gap-2 px-3 py-2">
            <input
                ref="fileInput"
                type="file"
                multiple
                accept="image/*,.pdf,.doc,.docx,.txt,.zip"
                class="hidden"
                @change="onFileSelect"
            />
            <Button
                type="button"
                variant="ghost"
                size="icon"
                class="h-9 w-9 shrink-0"
                :disabled="disabled || attachments.length >= 5"
                @click="fileInput?.click()"
            >
                <Paperclip class="size-4" />
            </Button>

            <div
                class="min-w-0 flex-1 rounded-lg border bg-muted/30 focus-within:ring-2 focus-within:ring-ring"
            >
                <EditorContent
                    :editor="editor"
                    class="prose prose-sm dark:prose-invert max-w-none [&_.tiptap]:outline-none [&_.tiptap_p.is-editor-empty:first-child::before]:pointer-events-none [&_.tiptap_p.is-editor-empty:first-child::before]:float-left [&_.tiptap_p.is-editor-empty:first-child::before]:h-0 [&_.tiptap_p.is-editor-empty:first-child::before]:text-muted-foreground [&_.tiptap_p.is-editor-empty:first-child::before]:content-[attr(data-placeholder)]"
                />
            </div>

            <Button
                type="button"
                size="icon"
                class="h-9 w-9 shrink-0"
                :disabled="disabled"
                @click="handleSend"
            >
                <SendHorizontal class="size-4" />
            </Button>
        </div>
    </div>
</template>
