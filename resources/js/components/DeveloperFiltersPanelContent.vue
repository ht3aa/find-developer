<script setup lang="ts">
import { Check, Copy, Sparkles } from 'lucide-vue-next';
import { computed } from 'vue';
import SearchableSelect from '@/components/SearchableSelect.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    availabilityTypeOptions,
    hasUrlsOptions,
    nullFieldFilterOptions,
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
    filterLocation: string[];
    isAvailable: string;
    isRecommended: string;
    yearsMin: string;
    yearsMax: string;
    jobTitleSelectOpen: boolean;
    skillSelectOpen: boolean;
    badgeSelectOpen: boolean;
    availabilityTypeSelectOpen: boolean;
    hasUrlsSelectOpen: boolean;
    locationSelectOpen: boolean;
    /** Super admin: filter rows where selected columns are null/empty. */
    showSuperAdminFilters: boolean;
    filterNullField: string[];
    nullFieldSelectOpen: boolean;
    paginationTotal: number | null;
    aiPromptText: string;
    aiPromptCopied: boolean;
    /** Narrow single-column layout for the left sidebar (vs. full-width sheet). */
    variant?: 'panel' | 'sidebar';
}>();

const isSidebar = computed(() => props.variant === 'sidebar');

const emit = defineEmits<{
    (e: 'update:filterJobTitle', v: string[]): void;
    (e: 'update:filterSkill', v: string[]): void;
    (e: 'update:filterBadge', v: string[]): void;
    (e: 'update:filterAvailabilityType', v: string[]): void;
    (e: 'update:filterHasUrls', v: string[]): void;
    (e: 'update:filterLocation', v: string[]): void;
    (e: 'update:isAvailable', v: string): void;
    (e: 'update:isRecommended', v: string): void;
    (e: 'update:yearsMin', v: string): void;
    (e: 'update:yearsMax', v: string): void;
    (e: 'update:jobTitleSelectOpen', v: boolean): void;
    (e: 'update:skillSelectOpen', v: boolean): void;
    (e: 'update:badgeSelectOpen', v: boolean): void;
    (e: 'update:availabilityTypeSelectOpen', v: boolean): void;
    (e: 'update:hasUrlsSelectOpen', v: boolean): void;
    (e: 'update:locationSelectOpen', v: boolean): void;
    (e: 'update:filterNullField', v: string[]): void;
    (e: 'update:nullFieldSelectOpen', v: boolean): void;
    (e: 'clearFilters'): void;
    (e: 'copyAiPrompt'): void;
}>();
</script>

<template>
    <div
        :class="
            isSidebar
                ? 'w-full pb-4'
                : 'mx-auto w-full max-w-4xl pr-8 pb-5 sm:pr-10 sm:pb-6'
        "
    >
        <p class="sr-only">
            Filter developers by job title, skills, badges, location,
            availability type, has URLs, availability status, recommended
            status, years of experience, and for super admins missing profile
            fields.
        </p>

        <div class="mb-4 border-b border-border/60 pb-4">
            <h2
                class="text-[11px] font-bold tracking-[0.14em] text-muted-foreground uppercase"
            >
                Custom filters
            </h2>
            <p class="mt-1.5 text-sm text-muted-foreground">
                Results update when you change any field below.
            </p>
        </div>

        <div class="grid w-full grid-cols-1 gap-4">
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
                <Label for="filter-location">Location</Label>
                <SearchableSelect
                    id="filter-location"
                    :model-value="props.filterLocation"
                    :open="props.locationSelectOpen"
                    options-url="/api/locations"
                    placeholder="e.g. Baghdad, Erbil"
                    multiple
                    :max-options="50"
                    @update:model-value="
                        emit(
                            'update:filterLocation',
                            normalizeSelectValue($event),
                        )
                    "
                    @update:open="emit('update:locationSelectOpen', $event)"
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
            <div
                v-if="props.showSuperAdminFilters"
                class="w-full space-y-2 rounded-xl border border-amber-500/35 bg-amber-500/5 p-3"
            >
                <Label
                    for="filter-null-field"
                    class="text-amber-950 dark:text-amber-100"
                >
                    Missing field (super admin)
                </Label>
                <p
                    class="text-xs leading-snug text-muted-foreground"
                    id="filter-null-field-hint"
                >
                    Show developers where any selected column is empty. Multiple
                    selections use OR (match if missing any of these fields).
                </p>
                <SearchableSelect
                    id="filter-null-field"
                    aria-describedby="filter-null-field-hint"
                    :model-value="props.filterNullField"
                    :open="props.nullFieldSelectOpen"
                    :options="nullFieldFilterOptions"
                    placeholder="e.g. Phone, Bio, CV"
                    multiple
                    @update:model-value="
                        emit(
                            'update:filterNullField',
                            normalizeSelectValue($event),
                        )
                    "
                    @update:open="emit('update:nullFieldSelectOpen', $event)"
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
        <div
            class="mt-8 flex justify-start border-t border-border/70 pt-6"
        >
            <Button
                type="button"
                variant="outline"
                class="min-h-10 w-full sm:w-auto"
                @click="emit('clearFilters')"
            >
                Clear all filters
            </Button>
        </div>

        <div
            class="mt-8 overflow-hidden rounded-2xl border border-border/80 bg-card shadow-sm ring-1 ring-black/[0.03] transition-all duration-200 dark:ring-white/[0.06]"
        >
            <div
                class="flex flex-col gap-4 p-4 sm:flex-row sm:items-start sm:justify-between sm:gap-6 sm:p-5"
            >
                <div class="flex min-w-0 flex-1 items-start gap-3 sm:gap-4">
                    <div
                        class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-primary/12 text-primary shadow-inner"
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
                    class="shrink-0 gap-2 border-primary/50 bg-background shadow-sm hover:bg-primary/5"
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
                class="border-t border-border/70 bg-muted/30 px-4 py-3 sm:px-5 sm:py-4"
            >
                <textarea
                    :value="props.aiPromptText"
                    readonly
                    rows="6"
                    class="w-full resize-none rounded-xl border border-border/70 bg-background px-3.5 py-3 font-mono text-sm leading-relaxed text-foreground shadow-inner selection:bg-primary/20 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none"
                    aria-label="AI prompt text"
                />
            </div>
        </div>
    </div>
</template>
