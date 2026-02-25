<script setup lang="ts">
import {
    Command,
    CommandContent,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { Button } from '@/components/ui/button';
import { ChevronsUpDown } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { cn } from '@/lib/utils';

const props = withDefaults(
    defineProps<{
        modelValue?: string | null;
        open?: boolean;
        options: { value: string; label: string }[];
        placeholder?: string;
        id?: string;
        class?: string;
        maxOptions?: number;
        allowClear?: boolean;
    }>(),
    {
        modelValue: null,
        placeholder: 'Search...',
        maxOptions: 50,
        allowClear: true,
    },
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | null): void;
    (e: 'update:open', value: boolean): void;
}>();

const isControlled = computed(() => props.open !== undefined);
const internalOpen = ref(false);
const open = computed({
    get: () => (isControlled.value ? props.open! : internalOpen.value),
    set: (v) => {
        if (!isControlled.value) internalOpen.value = v;
        emit('update:open', v);
    },
});

const selectedOption = computed(() =>
    props.options.find((o) => o.value === props.modelValue),
);

const displayValue = computed(() => selectedOption.value?.label ?? '');

const filteredOptions = computed(() =>
    props.options.slice(0, props.maxOptions),
);

const CLEAR_VALUE = '__CLEAR__';

function handleSelect(value: string | null): void {
    if (value === CLEAR_VALUE || value === null) {
        emit('update:modelValue', null);
    } else {
        const option = props.options.find(
            (o) => o.value === value || o.label === value,
        );
        emit('update:modelValue', option?.value ?? value);
    }
    open.value = false;
}

function onOpenChange(value: boolean): void {
    if (!isControlled.value) internalOpen.value = value;
    emit('update:open', value);
}
</script>

<template>
    <Popover
        :open="open"
        @update:open="onOpenChange"
    >
        <PopoverTrigger as-child>
            <Button
                :id="id"
                variant="outline"
                role="combobox"
                :aria-expanded="open"
                :aria-label="placeholder"
                :class="cn(
                    'flex h-9 w-full items-center justify-between font-normal',
                    props.class,
                )"
            >
                {{ displayValue || placeholder }}
                <ChevronsUpDown class="ml-2 size-4 shrink-0 opacity-50" />
            </Button>
        </PopoverTrigger>
        <PopoverContent
            class="w-[var(--reka-popover-trigger-width)] p-0"
            align="start"
        >
            <Command
                :model-value="selectedOption?.label ?? modelValue ?? ''"
                :ignore-filter="false"
                :open-on-focus="true"
                @update:model-value="handleSelect"
            >
                <CommandInput :placeholder="placeholder" />
                <CommandContent>
                    <CommandEmpty>No results found.</CommandEmpty>
                    <CommandList>
                        <CommandGroup>
                            <CommandItem
                                v-if="allowClear && modelValue"
                                :value="CLEAR_VALUE"
                                class="text-muted-foreground"
                            >
                                Clear selection
                            </CommandItem>
                            <CommandItem
                                v-for="opt in filteredOptions"
                                :key="opt.value"
                                :value="opt.label"
                            >
                                {{ opt.label }}
                            </CommandItem>
                        </CommandGroup>
                    </CommandList>
                </CommandContent>
            </Command>
        </PopoverContent>
    </Popover>
</template>
