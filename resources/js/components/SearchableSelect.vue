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
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Check, ChevronsUpDown, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { cn } from '@/lib/utils';

const props = withDefaults(
    defineProps<{
        modelValue?: string | string[] | null;
        multiple?: boolean;
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
        multiple: false,
        placeholder: 'Search...',
        maxOptions: 50,
        allowClear: true,
    },
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | string[] | null): void;
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

const selectedValues = computed(() => {
    const val = props.modelValue;
    if (val == null) return [];
    return Array.isArray(val) ? val : [val];
});

const selectedOptions = computed(() =>
    selectedValues.value
        .map((v) => props.options.find((o) => o.value === v))
        .filter(Boolean) as { value: string; label: string }[],
);

const selectedOption = computed(() => selectedOptions.value[0]);

const displayValue = computed(() => {
    if (props.multiple && selectedOptions.value.length > 0) {
        return selectedOptions.value.map((o) => o.label).join(', ');
    }
    return selectedOption.value?.label ?? '';
});

const commandModelValue = computed(() => {
    if (props.multiple) {
        return selectedOptions.value.map((o) => o.label);
    }
    return selectedOption.value?.label ?? props.modelValue ?? '';
});

const filteredOptions = computed(() =>
    props.options.slice(0, props.maxOptions),
);

const CLEAR_VALUE = '__CLEAR__';

function handleSelect(value: string | string[] | null): void {
    if (value === CLEAR_VALUE || value === null) {
        emit('update:modelValue', props.multiple ? [] : null);
    } else if (props.multiple && Array.isArray(value)) {
        const values = value
            .map((labelOrValue) => {
                const opt = props.options.find(
                    (o) => o.value === labelOrValue || o.label === labelOrValue,
                );
                return opt?.value ?? labelOrValue;
            })
            .filter(Boolean);
        emit('update:modelValue', values);
    } else if (props.multiple) {
        const labelOrValue = value as string;
        const opt = props.options.find(
            (o) => o.value === labelOrValue || o.label === labelOrValue,
        );
        const newValue = opt?.value ?? labelOrValue;
        const current = selectedValues.value;
        const exists = current.includes(newValue);
        const next = exists
            ? current.filter((v) => v !== newValue)
            : [...current, newValue];
        emit('update:modelValue', next.length > 0 ? next : []);
    } else {
        const option = props.options.find(
            (o) => o.value === value || o.label === value,
        );
        emit('update:modelValue', option?.value ?? (value as string));
        open.value = false;
    }
}

function removeValue(value: string, event: Event): void {
    event.stopPropagation();
    const next = selectedValues.value.filter((v) => v !== value);
    emit('update:modelValue', next.length > 0 ? next : []);
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
                :aria-multiselectable="multiple"
                :class="cn(
                    'flex h-auto min-h-9 w-full items-center justify-between gap-2 font-normal',
                    multiple && selectedOptions.length > 0 && 'py-1.5',
                    props.class,
                )"
            >
                <span
                    v-if="multiple && selectedOptions.length > 0"
                    class="flex flex-1 flex-wrap items-center gap-1 overflow-hidden"
                >
                    <Badge
                        v-for="opt in selectedOptions"
                        :key="opt.value"
                        variant="secondary"
                        class="gap-1 pr-1 font-normal"
                    >
                        {{ opt.label }}
                        <button
                            type="button"
                            class="rounded-full outline-none ring-offset-background hover:bg-secondary focus:ring-2 focus:ring-ring focus:ring-offset-2"
                            aria-label="Remove"
                            @click="removeValue(opt.value, $event)"
                        >
                            <X class="size-3.5" />
                        </button>
                    </Badge>
                </span>
                <span
                    v-else
                    class="flex-1 truncate text-left"
                >
                    {{ displayValue || placeholder }}
                </span>
                <ChevronsUpDown class="ml-2 size-4 shrink-0 opacity-50" />
            </Button>
        </PopoverTrigger>
        <PopoverContent
            class="w-[var(--reka-popover-trigger-width)] p-0"
            align="start"
        >
            <Command
                :model-value="commandModelValue"
                :multiple="multiple"
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
                                v-if="allowClear && (modelValue != null && (Array.isArray(modelValue) ? modelValue.length > 0 : true))"
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
                                <Check
                                    v-if="multiple"
                                    :class="cn(
                                        'mr-2 size-4 shrink-0',
                                        selectedValues.includes(opt.value)
                                            ? 'opacity-100'
                                            : 'opacity-0',
                                    )"
                                />
                                {{ opt.label }}
                            </CommandItem>
                        </CommandGroup>
                    </CommandList>
                </CommandContent>
            </Command>
        </PopoverContent>
    </Popover>
</template>
