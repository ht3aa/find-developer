<script setup lang="ts">
import {
    Combobox,
    ComboboxAnchor,
    ComboboxCancel,
    ComboboxEmpty,
    ComboboxGroup,
    ComboboxInput,
    ComboboxItem,
    ComboboxItemIndicator,
    ComboboxList,
    ComboboxTrigger,
    ComboboxViewport,
} from '@/components/ui/combobox';
import { Badge } from '@/components/ui/badge';
import { useDebounceFn } from '@vueuse/core';
import { Check, ChevronsUpDown, Search, X } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import { useId } from 'vue';
import { cn } from '@/lib/utils';

const instanceId = useId();

const props = withDefaults(
    defineProps<{
        modelValue?: string | string[] | null;
        multiple?: boolean;
        open?: boolean;
        options?: { value: string; label: string }[];
        optionsUrl?: string;
        placeholder?: string;
        id?: string;
        class?: string;
        maxOptions?: number;
        allowClear?: boolean;
    }>(),
    {
        modelValue: null,
        multiple: false,
        options: () => [],
        placeholder: 'Search...',
        maxOptions: 50,
        allowClear: true,
    },
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | string[] | null): void;
    (e: 'update:open', value: boolean): void;
}>();

const searchTerm = ref('');
const fetchedOptions = ref<{ value: string; label: string }[]>([]);
const optionsLoading = ref(false);

const selectedValues = computed(() => {
    const val = props.modelValue;
    if (val == null) return [];
    return Array.isArray(val) ? val : [val];
});

const selectedOptions = computed(() =>
    selectedValues.value.map((v) => {
        const opt = displayOptions.value.find((o) => o.value === v || o.label === v);
        return opt ?? { value: v, label: v };
    }),
);

const displayValue = computed(() => {
    if (props.multiple && selectedOptions.value.length > 0) {
        return selectedOptions.value.map((o) => o.label).join(', ');
    }
    return selectedOptions.value[0]?.label ?? '';
});

const displayOptions = computed(() => {
    if (props.optionsUrl) {
        const selected = selectedValues.value
            .map((v) => {
                const opt = fetchedOptions.value.find((o) => o.value === v || o.label === v);
                return opt ?? { value: v, label: v };
            })
            .filter((o) => o.value);
        const fromFetched = fetchedOptions.value.filter(
            (o) => !selected.some((s) => s.value === o.value),
        );
        return [...selected, ...fromFetched].slice(0, props.maxOptions);
    }
    return (props.options ?? []).slice(0, props.maxOptions);
});

async function fetchOptions(): Promise<void> {
    if (!props.optionsUrl) return;
    optionsLoading.value = true;
    try {
        const url = new URL(props.optionsUrl, window.location.origin);
        if (searchTerm.value) url.searchParams.set('search', searchTerm.value);
        const res = await fetch(url.toString());
        if (!res.ok) throw new Error('Failed to fetch options');
        const json = await res.json();
        fetchedOptions.value = Array.isArray(json.data) ? json.data : [];
    } catch {
        fetchedOptions.value = [];
    } finally {
        optionsLoading.value = false;
    }
}

const debouncedFetch = useDebounceFn(() => {
    if (props.optionsUrl) fetchOptions();
}, 300);

watch(
    () => searchTerm.value,
    () => {
        if (props.optionsUrl) debouncedFetch();
    },
);

onMounted(() => {
    if (props.optionsUrl) fetchOptions();
});

function removeValue(value: string, event: Event): void {
    event.stopPropagation();
    const next = selectedValues.value.filter((v) => v !== value);
    emit('update:modelValue', next.length > 0 ? next : []);
}
</script>

<template>
    <Combobox
        :name="id ?? instanceId"
        :model-value="multiple ? selectedValues : (selectedValues[0] ?? null)"
        :multiple="multiple"
        :open="effectiveOpen"
        :open-on-click="true"
        :ignore-filter="!!optionsUrl"
        :reset-search-term-on-select="false"
        @update:model-value="emit('update:modelValue', multiple ? (Array.isArray($event) ? $event : []) : (Array.isArray($event) ? $event[0] ?? null : $event ?? null))"
        @update:open="handleOpenChange"
    >
        <ComboboxAnchor
            :class="cn(
                'flex h-auto min-h-9 w-full items-center justify-between gap-2 rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm transition-colors',
                'focus-within:outline-none focus-within:ring-1 focus-within:ring-ring',
                props.class,
            )"
        >
            <ComboboxTrigger
                :id="id"
                as-child
                class="flex min-w-0 flex-1 cursor-pointer items-center gap-2 outline-none"
            >
                <button
                    type="button"
                    class="flex min-w-0 flex-1 cursor-pointer items-center gap-2 bg-transparent text-left outline-none"
                >
                    <span
                        v-if="multiple && selectedOptions.length > 0"
                        class="flex flex-1 flex-wrap items-center gap-1"
                    >
                        <Badge
                            v-for="(opt, i) in selectedOptions"
                            :key="`${instanceId}-badge-${opt.value}-${i}`"
                            variant="secondary"
                            class="gap-1 pr-1 font-normal"
                        >
                            {{ opt.label }}
                            <button
                                v-if="allowClear"
                                type="button"
                                class="rounded-full outline-none ring-offset-background hover:bg-secondary focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                aria-label="Remove"
                                @click.stop="removeValue(opt.value, $event)"
                            >
                                <X class="size-3.5" />
                            </button>
                        </Badge>
                    </span>
                    <span
                        v-else
                        :class="cn(
                            'flex-1 truncate text-left',
                            !displayValue && 'text-muted-foreground',
                        )"
                    >
                        {{ displayValue || placeholder }}
                    </span>
                    <ChevronsUpDown class="size-4 shrink-0 opacity-50" />
                </button>
            </ComboboxTrigger>
            <ComboboxCancel class="sr-only" />
        </ComboboxAnchor>

        <ComboboxList
            class="w-[var(--reka-combobox-trigger-width)] max-h-[300px] p-0"
            align="start"
        >
            <div class="relative">
                <ComboboxInput
                    v-model="searchTerm"
                    :placeholder="placeholder"
                    class="flex-1 border-0 rounded-none pr-10"
                />
                <button
                    v-if="optionsUrl"
                    type="button"
                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1.5 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                    aria-label="Search"
                    :disabled="optionsLoading"
                    @click="fetchOptions"
                >
                    <Search class="size-4" />
                </button>
            </div>
            <ComboboxViewport>
                <ComboboxEmpty>
                    {{ optionsLoading ? 'Searching...' : 'No results found.' }}
                </ComboboxEmpty>
                <ComboboxGroup>
                    <ComboboxItem
                        v-for="(opt, i) in displayOptions"
                        :key="`${instanceId}-item-${opt.value}-${i}`"
                        :value="opt.value"
                        :text-value="opt.label"
                    >
                        <Check
                            v-if="multiple"
                            :class="cn(
                                'mr-2 size-4 shrink-0',
                                selectedValues.includes(opt.value) ? 'opacity-100' : 'opacity-0',
                            )"
                        />
                        <ComboboxItemIndicator
                            v-else
                            class="mr-2"
                        >
                            <Check class="size-4" />
                        </ComboboxItemIndicator>
                        {{ opt.label }}
                    </ComboboxItem>
                </ComboboxGroup>
            </ComboboxViewport>
        </ComboboxList>
    </Combobox>
</template>
