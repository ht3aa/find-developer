<script setup lang="ts">
import { Check, Copy, Sparkles, Users } from 'lucide-vue-next';
import SearchableSelect from '@/components/SearchableSelect.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { SheetDescription, SheetTitle } from '@/components/ui/sheet';
import {
    availabilityTypeOptions,
    hasUrlsOptions,
} from '@/utils/developerEnums';

function normalizeSelectValue(event: string | string[] | null): string[] {
    if (event == null) return [];
    return Array.isArray(event) ? event : event ? [event] : [];
}

function getSelectValue(event: Event): string {
    const target = event.target;
    return target && 'value' in target
        ? String((target as { value: string }).value)
        : 'all';
}

const props = defineProps<{
    filterJobTitle: string[];
    filterSkill: string[];
    filterBadge: string[];
    filterAvailabilityType: string[];
    filterHasUrls: string[];
    isAvailable: string;
    isRecommended: string;
    yearsMin: string;
    yearsMax: string;
    jobTitleSelectOpen: boolean;
    skillSelectOpen: boolean;
    badgeSelectOpen: boolean;
    availabilityTypeSelectOpen: boolean;
    hasUrlsSelectOpen: boolean;
    paginationTotal: number | null;
    aiPromptText: string;
    aiPromptCopied: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:filterJobTitle', v: string[]): void;
    (e: 'update:filterSkill', v: string[]): void;
    (e: 'update:filterBadge', v: string[]): void;
    (e: 'update:filterAvailabilityType', v: string[]): void;
    (e: 'update:filterHasUrls', v: string[]): void;
    (e: 'update:isAvailable', v: string): void;
    (e: 'update:isRecommended', v: string): void;
    (e: 'update:yearsMin', v: string): void;
    (e: 'update:yearsMax', v: string): void;
    (e: 'update:jobTitleSelectOpen', v: boolean): void;
    (e: 'update:skillSelectOpen', v: boolean): void;
    (e: 'update:badgeSelectOpen', v: boolean): void;
    (e: 'update:availabilityTypeSelectOpen', v: boolean): void;
    (e: 'update:hasUrlsSelectOpen', v: boolean): void;
    (e: 'applyFilters'): void;
    (e: 'clearFilters'): void;
    (e: 'copyAiPrompt'): void;
}>();
</script>

<template>
    <div class="mx-auto w-full max-w-4xl py-6 pr-10">
        <div class="mb-4 flex flex-wrap items-center gap-2 sm:gap-3">
            <SheetTitle class="text-lg font-semibold">
                Advanced filters
            </SheetTitle>
            <div
                v-if="props.paginationTotal !== null"
                class="inline-flex items-center gap-1.5 rounded-lg border border-primary/20 bg-primary/10 px-3 py-1.5"
                aria-live="polite"
            >
                <Users
                    class="size-4 shrink-0 text-primary"
                    aria-hidden="true"
                />
                <span
                    class="text-base font-semibold tracking-tight text-foreground tabular-nums"
                >
                    {{ props.paginationTotal }}
                </span>
                <span class="text-sm text-muted-foreground">
                    developer{{ props.paginationTotal === 1 ? '' : 's' }}
                </span>
            </div>
        </div>
        <SheetDescription class="sr-only">
            Filter developers by job title, skills, badges, availability type,
            has URLs, availability status, recommended status, and years of
            experience.
        </SheetDescription>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="space-y-2">
                <Label for="filter-job-title">Job title</Label>
                <SearchableSelect
                    id="filter-job-title"
                    :model-value="props.filterJobTitle"
                    :open="props.jobTitleSelectOpen"
                    options-url="/api/job-titles"
                    placeholder="e.g. Backend Developer"
                    multiple
                    :max-options="50"
                    @update:model-value="
                        emit(
                            'update:filterJobTitle',
                            normalizeSelectValue($event),
                        )
                    "
                    @update:open="emit('update:jobTitleSelectOpen', $event)"
                />
            </div>
            <div class="space-y-2">
                <Label for="filter-skill">Skill</Label>
                <SearchableSelect
                    id="filter-skill"
                    :model-value="props.filterSkill"
                    :open="props.skillSelectOpen"
                    options-url="/api/skills"
                    placeholder="e.g. Laravel, Vue"
                    multiple
                    :max-options="50"
                    @update:model-value="
                        emit('update:filterSkill', normalizeSelectValue($event))
                    "
                    @update:open="emit('update:skillSelectOpen', $event)"
                />
            </div>
            <div class="space-y-2">
                <Label for="filter-badge">Badge</Label>
                <SearchableSelect
                    id="filter-badge"
                    :model-value="props.filterBadge"
                    :open="props.badgeSelectOpen"
                    options-url="/api/badges"
                    placeholder="e.g. Laravel Expert"
                    multiple
                    :max-options="50"
                    @update:model-value="
                        emit('update:filterBadge', normalizeSelectValue($event))
                    "
                    @update:open="emit('update:badgeSelectOpen', $event)"
                />
            </div>
            <div class="space-y-2">
                <Label for="filter-availability-type">Availability type</Label>
                <SearchableSelect
                    id="filter-availability-type"
                    :model-value="props.filterAvailabilityType"
                    :open="props.availabilityTypeSelectOpen"
                    :options="availabilityTypeOptions"
                    placeholder="e.g. Full-time, Remote"
                    multiple
                    @update:model-value="
                        emit(
                            'update:filterAvailabilityType',
                            normalizeSelectValue($event),
                        )
                    "
                    @update:open="
                        emit('update:availabilityTypeSelectOpen', $event)
                    "
                />
            </div>
            <div class="space-y-2">
                <Label for="filter-has-urls">Has URLs</Label>
                <SearchableSelect
                    id="filter-has-urls"
                    :model-value="props.filterHasUrls"
                    :open="props.hasUrlsSelectOpen"
                    :options="hasUrlsOptions"
                    placeholder="e.g. GitHub, LinkedIn"
                    multiple
                    @update:model-value="
                        emit(
                            'update:filterHasUrls',
                            normalizeSelectValue($event),
                        )
                    "
                    @update:open="emit('update:hasUrlsSelectOpen', $event)"
                />
            </div>
            <div class="space-y-2">
                <Label for="filter-is-available">Availability</Label>
                <select
                    id="filter-is-available"
                    :value="props.isAvailable"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none"
                    @change="emit('update:isAvailable', getSelectValue($event))"
                >
                    <option value="all">All</option>
                    <option value="1">Available</option>
                    <option value="0">Not available</option>
                </select>
            </div>
            <div class="space-y-2">
                <Label for="filter-is-recommended">Recommended</Label>
                <select
                    id="filter-is-recommended"
                    :value="props.isRecommended"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none"
                    @change="
                        emit('update:isRecommended', getSelectValue($event))
                    "
                >
                    <option value="all">All</option>
                    <option value="1">Recommended</option>
                    <option value="0">Not recommended</option>
                </select>
            </div>
            <div class="space-y-2">
                <Label for="filter-years-min">Min. years of experience</Label>
                <Input
                    id="filter-years-min"
                    :model-value="props.yearsMin"
                    type="number"
                    min="0"
                    placeholder="0"
                    class="w-full"
                    @update:model-value="
                        emit('update:yearsMin', String($event ?? ''))
                    "
                />
            </div>
            <div class="space-y-2">
                <Label for="filter-years-max">Max. years of experience</Label>
                <Input
                    id="filter-years-max"
                    :model-value="props.yearsMax"
                    type="number"
                    min="0"
                    placeholder="Any"
                    class="w-full"
                    @update:model-value="
                        emit('update:yearsMax', String($event ?? ''))
                    "
                />
            </div>
        </div>
        <div class="mt-6 flex flex-wrap items-center gap-2">
            <Button @click="emit('applyFilters')">Apply filters</Button>
            <Button
                variant="ghost"
                class="text-muted-foreground"
                @click="emit('clearFilters')"
            >
                Clear all
            </Button>
        </div>

        <div
            class="mt-6 overflow-hidden rounded-xl border border-border bg-card shadow-sm transition-all duration-200"
        >
            <div
                class="flex flex-col gap-4 p-4 sm:flex-row sm:items-start sm:justify-between sm:gap-6"
            >
                <div class="flex min-w-0 flex-1 items-start gap-3">
                    <div
                        class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                    >
                        <Sparkles class="size-5" aria-hidden="true" />
                    </div>
                    <div class="min-w-0 flex-1 space-y-1">
                        <Label
                            class="text-base font-semibold tracking-tight text-foreground"
                        >
                            AI prompt
                        </Label>
                        <p class="text-sm text-muted-foreground">
                            Copy this text and share it with an AI assistant so
                            it can search this site using your current filters
                            and find the best match.
                        </p>
                    </div>
                </div>
                <Button
                    type="button"
                    variant="outline"
                    size="default"
                    class="shrink-0 gap-2 border-primary"
                    :aria-label="
                        props.aiPromptCopied ? 'Copied' : 'Copy AI prompt'
                    "
                    @click="emit('copyAiPrompt')"
                >
                    <Check
                        v-if="props.aiPromptCopied"
                        class="size-4 text-green-600 dark:text-green-400"
                        aria-hidden="true"
                    />
                    <Copy v-else class="size-4" aria-hidden="true" />
                    <span>{{ props.aiPromptCopied ? 'Copied' : 'Copy' }}</span>
                </Button>
            </div>
            <div
                class="border-t border-border bg-muted/20 px-4 py-3 sm:px-4 sm:py-3"
            >
                <textarea
                    :value="props.aiPromptText"
                    readonly
                    rows="6"
                    class="w-full resize-none rounded-lg border border-border/60 bg-background px-3.5 py-3 font-mono text-sm leading-relaxed text-foreground shadow-inner selection:bg-primary/20 focus-visible:ring-2 focus-visible:ring-primary/20 focus-visible:outline-none"
                    aria-label="AI prompt text"
                />
            </div>
        </div>
    </div>
</template>
