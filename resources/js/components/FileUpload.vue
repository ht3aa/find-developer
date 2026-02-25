<script setup lang="ts">
import { FileUp, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { cn } from '@/lib/utils';

const props = withDefaults(
    defineProps<{
        modelValue?: File | null;
        id?: string;
        label?: string;
        accept?: string;
        existingUrl?: string | null;
        existingLabel?: string;
        error?: string | null;
    }>(),
    {
        modelValue: null,
        label: 'File',
        accept: '*/*',
        existingUrl: null,
        existingLabel: 'View current file',
        error: null,
    },
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: File | null): void;
}>();

const inputRef = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);

function onChange(event: Event): void {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    emit('update:modelValue', file ?? null);
}

function clear(): void {
    emit('update:modelValue', null);
    if (inputRef.value) {
        inputRef.value.value = '';
    }
}

function onDragOver(event: DragEvent): void {
    event.preventDefault();
    event.stopPropagation();
    isDragging.value = true;
}

function onDragLeave(): void {
    isDragging.value = false;
}

function onDrop(event: DragEvent): void {
    event.preventDefault();
    event.stopPropagation();
    isDragging.value = false;
    const file = event.dataTransfer?.files?.[0];
    if (file) {
        emit('update:modelValue', file);
        if (inputRef.value) {
            const dt = new DataTransfer();
            dt.items.add(file);
            inputRef.value.files = dt.files;
        }
    }
}

function triggerInput(): void {
    inputRef.value?.click();
}

watch(
    () => props.modelValue,
    (val) => {
        if (!val && inputRef.value) {
            inputRef.value.value = '';
        }
    },
);

defineExpose({ clear, inputRef });
</script>

<template>
    <div class="grid gap-2">
        <Label v-if="label" :for="id">{{ label }}</Label>
        <div
            :class="cn(
                'relative flex min-h-[120px] flex-col items-center justify-center gap-3 rounded-lg border-2 border-dashed px-4 py-6 transition-all duration-200',
                'border-input bg-muted/30 hover:border-primary/40 hover:bg-muted/50',
                'focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20',
                isDragging && 'border-primary bg-primary/5 ring-2 ring-primary/20',
            )"
            @dragover="onDragOver"
            @dragleave="onDragLeave"
            @drop="onDrop"
        >
            <input
                :id="id"
                ref="inputRef"
                type="file"
                :accept="accept"
                class="absolute inset-0 cursor-pointer opacity-0"
                @change="onChange"
            />

            <template v-if="modelValue">
                <div class="relative z-10 flex items-center gap-3 rounded-md bg-background/80 px-4 py-2.5 shadow-sm ring-1 ring-border/50">
                    <FileUp class="size-5 shrink-0 text-primary" />
                    <span class="truncate text-sm font-medium text-foreground max-w-[200px]">
                        {{ modelValue.name }}
                    </span>
                    <button
                        type="button"
                        class="shrink-0 rounded-full p-1 text-muted-foreground transition-colors hover:bg-destructive/10 hover:text-destructive focus:outline-none focus:ring-2 focus:ring-ring"
                        aria-label="Remove file"
                        @click.stop="clear"
                    >
                        <X class="size-4" />
                    </button>
                </div>
                <p class="text-xs text-muted-foreground">
                    Click or drag a new file to replace
                </p>
            </template>

            <template v-else-if="existingUrl">
                <a
                    :href="existingUrl"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="relative z-10 inline-flex items-center gap-2 rounded-md bg-primary/10 px-4 py-2.5 text-sm font-medium text-primary ring-1 ring-primary/20 transition-colors hover:bg-primary/20 hover:ring-primary/30"
                    @click.stop
                >
                    <FileUp class="size-5" />
                    {{ existingLabel }}
                </a>
                <p class="text-xs text-muted-foreground">
                    Click or drag a file to upload a new one
                </p>
            </template>

            <template v-else>
                <div
                    role="button"
                    tabindex="0"
                    class="flex cursor-pointer flex-col items-center gap-2"
                    @click="triggerInput"
                    @keydown.enter.prevent="triggerInput"
                    @keydown.space.prevent="triggerInput"
                >
                    <div class="flex size-12 items-center justify-center rounded-full bg-primary/10 text-primary">
                        <FileUp class="size-6" />
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-medium text-foreground">
                            Drop your file here or <span class="text-primary underline">browse</span>
                        </p>
                        <p class="mt-0.5 text-xs text-muted-foreground">
                            Supports {{ accept === '.pdf,application/pdf' ? 'PDF' : 'all files' }}
                        </p>
                    </div>
                </div>
            </template>
        </div>
        <InputError :message="error" />
    </div>
</template>
