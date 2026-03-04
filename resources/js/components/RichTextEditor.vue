<script setup lang="ts">
import { Placeholder } from '@tiptap/extension-placeholder';
import StarterKit from '@tiptap/starter-kit';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import {
    Bold,
    Italic,
    List,
    ListOrdered,
    Quote,
    Heading2,
    Code,
} from 'lucide-vue-next';
import { watch } from 'vue';
import { Button } from '@/components/ui/button';

const props = withDefaults(
    defineProps<{
        modelValue?: string;
        placeholder?: string;
        disabled?: boolean;
    }>(),
    {
        modelValue: '',
        placeholder: 'Write your content…',
        disabled: false,
    },
);

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const editor = useEditor({
    content: props.modelValue || '',
    editable: !props.disabled,
    extensions: [
        StarterKit,
        Placeholder.configure({
            placeholder: props.placeholder,
        }),
    ],
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML());
    },
});

watch(
    () => props.modelValue,
    (value) => {
        const current = editor.value?.getHTML() ?? '';
        if (value !== current && editor.value) {
            editor.value.commands.setContent(value ?? '', false);
        }
    },
);

watch(
    () => props.disabled,
    (disabled) => {
        editor.value?.setEditable(!disabled);
    },
);
</script>

<template>
    <div
        class="rounded-md border border-input bg-background focus-within:ring-2 focus-within:ring-ring focus-within:ring-offset-2"
        :class="{ 'opacity-60': disabled }"
    >
        <div
            v-if="editor"
            class="flex flex-wrap items-center gap-0.5 border-b border-input p-1"
        >
            <Button
                type="button"
                variant="ghost"
                size="icon"
                class="h-8 w-8"
                :class="{ 'bg-muted': editor.isActive('bold') }"
                :disabled="disabled"
                @click="editor.chain().focus().toggleBold().run()"
            >
                <Bold class="h-4 w-4" />
            </Button>
            <Button
                type="button"
                variant="ghost"
                size="icon"
                class="h-8 w-8"
                :class="{ 'bg-muted': editor.isActive('italic') }"
                :disabled="disabled"
                @click="editor.chain().focus().toggleItalic().run()"
            >
                <Italic class="h-4 w-4" />
            </Button>
            <Button
                type="button"
                variant="ghost"
                size="icon"
                class="h-8 w-8"
                :class="{
                    'bg-muted': editor.isActive('heading', { level: 2 }),
                }"
                :disabled="disabled"
                @click="
                    editor.chain().focus().toggleHeading({ level: 2 }).run()
                "
            >
                <Heading2 class="h-4 w-4" />
            </Button>
            <Button
                type="button"
                variant="ghost"
                size="icon"
                class="h-8 w-8"
                :class="{ 'bg-muted': editor.isActive('bulletList') }"
                :disabled="disabled"
                @click="editor.chain().focus().toggleBulletList().run()"
            >
                <List class="h-4 w-4" />
            </Button>
            <Button
                type="button"
                variant="ghost"
                size="icon"
                class="h-8 w-8"
                :class="{ 'bg-muted': editor.isActive('orderedList') }"
                :disabled="disabled"
                @click="editor.chain().focus().toggleOrderedList().run()"
            >
                <ListOrdered class="h-4 w-4" />
            </Button>
            <Button
                type="button"
                variant="ghost"
                size="icon"
                class="h-8 w-8"
                :class="{ 'bg-muted': editor.isActive('blockquote') }"
                :disabled="disabled"
                @click="editor.chain().focus().toggleBlockquote().run()"
            >
                <Quote class="h-4 w-4" />
            </Button>
            <Button
                type="button"
                variant="ghost"
                size="icon"
                class="h-8 w-8"
                :class="{ 'bg-muted': editor.isActive('code') }"
                :disabled="disabled"
                @click="editor.chain().focus().toggleCode().run()"
            >
                <Code class="h-4 w-4" />
            </Button>
        </div>
        <EditorContent
            :editor="editor"
            class="prose prose-sm dark:prose-invert min-h-[200px] max-w-none px-3 py-2 [&_.tiptap]:min-h-[200px] [&_.tiptap]:outline-none [&_.tiptap_p.is-editor-empty:first-child::before]:pointer-events-none [&_.tiptap_p.is-editor-empty:first-child::before]:float-left [&_.tiptap_p.is-editor-empty:first-child::before]:h-0 [&_.tiptap_p.is-editor-empty:first-child::before]:text-muted-foreground [&_.tiptap_p.is-editor-empty:first-child::before]:content-[attr(data-placeholder)]"
        />
    </div>
</template>
