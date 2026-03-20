<script setup lang="ts">
import {
    Check,
    ChevronDown,
    Copy,
    Layers,
    Sparkles,
    Users,
} from 'lucide-vue-next';
import { ref } from 'vue';
import SearchableSelect from '@/components/SearchableSelect.vue';
import { Button } from '@/components/ui/button';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { SheetDescription, SheetTitle } from '@/components/ui/sheet';
import {
    DEVELOPER_FILTER_PRESET_GROUPS,
    DEVELOPER_FILTER_PRESETS,
    type DeveloperFilterPreset,
    type DeveloperFilterPresetGroup,
} from '@/lib/developerFilterPresets';
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
    /** Used so preset selection is cleared when the user types in the main search bar. */
    searchQuery: string;
    /** Selected quick preset ids (multi-select, combined with OR on the server). */
    selectedPresetIds: string[];
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
    (e: 'togglePreset', preset: DeveloperFilterPreset): void;
}>();

function presetsForGroup(
    key: DeveloperFilterPresetGroup,
): DeveloperFilterPreset[] {
    return DEVELOPER_FILTER_PRESETS.filter((p) => p.group === key);
}

function presetGroupPanelClass(key: DeveloperFilterPresetGroup): string {
    const accents: Record<DeveloperFilterPresetGroup, string> = {
        frontend:
            'border-l-[3px] border-l-violet-500/75 pl-3 sm:pl-4 dark:border-l-violet-400/65',
        backend:
            'border-l-[3px] border-l-sky-500/75 pl-3 sm:pl-4 dark:border-l-sky-400/65',
        fullstack:
            'border-l-[3px] border-l-amber-500/75 pl-3 sm:pl-4 dark:border-l-amber-400/65',
    };
    return accents[key];
}

function isPresetSelected(id: string): boolean {
    return props.selectedPresetIds.includes(id);
}

const presetsSectionOpen = ref(false);
</script>

<template>
    <div class="mx-auto w-full max-w-4xl pb-5 pr-8 sm:pb-6 sm:pr-10">
        <div
            class="sticky top-0 z-10 mb-6 flex flex-col gap-4 rounded-2xl border border-border/80 bg-gradient-to-br from-primary/[0.07] via-card/95 to-card/95 p-4 shadow-md shadow-black/5 ring-1 ring-black/[0.04] backdrop-blur-md supports-[backdrop-filter]:via-card/90 supports-[backdrop-filter]:to-card/90 sm:flex-row sm:items-center sm:justify-between sm:gap-6 sm:p-5 dark:ring-white/[0.06]"
        >
            <div class="min-w-0 space-y-1">
                <SheetTitle
                    class="text-xl font-semibold tracking-tight text-foreground"
                >
                    Advanced filters
                </SheetTitle>
                <p class="text-sm leading-snug text-muted-foreground">
                    Combine role presets (OR) or fine-tune with custom fields.
                </p>
            </div>
            <div
                v-if="props.paginationTotal !== null"
                class="inline-flex shrink-0 items-center gap-2 self-start rounded-xl border border-primary/25 bg-primary/10 px-3.5 py-2 sm:self-center"
                aria-live="polite"
            >
                <div
                    class="flex size-9 items-center justify-center rounded-lg bg-primary/15 text-primary"
                >
                    <Users class="size-4 shrink-0" aria-hidden="true" />
                </div>
                <div class="leading-tight">
                    <span
                        class="block text-lg font-bold tracking-tight text-foreground tabular-nums"
                    >
                        {{ props.paginationTotal }}
                    </span>
                    <span class="text-xs font-medium text-muted-foreground">
                        matching
                        {{ props.paginationTotal === 1 ? 'developer' : 'developers' }}
                    </span>
                </div>
            </div>
        </div>
        <SheetDescription class="sr-only">
            Filter developers by job title, skills, badges, availability type,
            has URLs, availability status, recommended status, years of
            experience, and quick role presets.
        </SheetDescription>

        <Collapsible
            v-model:open="presetsSectionOpen"
            class="mb-8 overflow-hidden rounded-2xl border border-border/80 bg-card shadow-sm ring-1 ring-black/[0.03] dark:ring-white/[0.06]"
        >
            <CollapsibleTrigger
                class="flex w-full items-center justify-between gap-3 rounded-t-2xl px-4 py-4 text-left transition-colors hover:bg-muted/30 sm:px-5 sm:py-4 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background"
            >
                <div class="flex min-w-0 flex-1 items-center gap-3 sm:gap-4">
                    <div
                        class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-primary/12 text-primary shadow-inner"
                    >
                        <Layers class="size-5" aria-hidden="true" />
                    </div>
                    <div class="min-w-0">
                        <p
                            class="text-[11px] font-bold tracking-[0.12em] text-primary uppercase sm:text-xs"
                        >
                            Quick role filters
                        </p>
                        <p
                            class="mt-0.5 text-sm font-medium text-foreground sm:text-base"
                        >
                            Tap to select multiple — results match
                            <span class="whitespace-nowrap">any</span> chosen
                            band
                        </p>
                    </div>
                </div>
                <span
                    class="flex size-9 shrink-0 items-center justify-center rounded-full border border-border/80 bg-muted/50 text-muted-foreground transition-transform duration-200"
                    :class="{ 'rotate-180': presetsSectionOpen }"
                >
                    <ChevronDown class="size-4" aria-hidden="true" />
                </span>
            </CollapsibleTrigger>
            <CollapsibleContent
                class="overflow-hidden border-t border-border/70 bg-muted/25"
            >
                <div class="space-y-5 px-4 py-5 sm:px-6 sm:py-6">
                    <p
                        class="text-sm leading-relaxed text-muted-foreground"
                    >
                        Each chip is a
                        <strong class="font-medium text-foreground"
                            >job title + experience band</strong
                        >. Select several to widen the pool (OR). Presets replace
                        the custom job title and min/max years fields until you
                        change those fields or deselect all presets. Use
                        <span class="font-medium text-foreground"
                            >Apply filters</span
                        >
                        after editing skills or other fields below.
                    </p>
                    <div
                        class="flex flex-wrap gap-2 border-b border-border/60 pb-5"
                        aria-hidden="true"
                    >
                        <span
                            class="inline-flex items-center rounded-full bg-background px-2.5 py-1 text-[11px] font-medium text-muted-foreground ring-1 ring-border/80"
                            >Junior ≤2 yrs</span
                        >
                        <span
                            class="inline-flex items-center rounded-full bg-background px-2.5 py-1 text-[11px] font-medium text-muted-foreground ring-1 ring-border/80"
                            >Mid 3–5 yrs</span
                        >
                        <span
                            class="inline-flex items-center rounded-full bg-background px-2.5 py-1 text-[11px] font-medium text-muted-foreground ring-1 ring-border/80"
                            >Senior 6+ yrs</span
                        >
                    </div>
                    <div class="space-y-5">
                        <div
                            v-for="section in DEVELOPER_FILTER_PRESET_GROUPS"
                            :key="section.key"
                            class="rounded-xl border border-border/70 bg-background/80 py-3 shadow-sm backdrop-blur-sm sm:py-4"
                            :class="presetGroupPanelClass(section.key)"
                        >
                            <p
                                class="mb-3 text-xs font-semibold tracking-wide text-foreground/90 uppercase sm:text-[13px]"
                            >
                                {{ section.title }}
                            </p>
                            <div
                                class="grid grid-cols-3 gap-2 sm:gap-3"
                                role="group"
                                :aria-label="`${section.title} experience presets`"
                            >
                                <button
                                    v-for="preset in presetsForGroup(section.key)"
                                    :key="preset.id"
                                    type="button"
                                    :aria-pressed="isPresetSelected(preset.id)"
                                    class="flex min-h-11 min-w-0 flex-col items-center justify-center rounded-xl border px-2 py-2.5 text-center text-sm font-semibold tracking-tight transition-all duration-150 sm:min-h-12 sm:px-3 sm:py-3 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background active:scale-[0.98]"
                                    :class="
                                        isPresetSelected(preset.id)
                                            ? 'border-primary bg-primary text-primary-foreground shadow-md shadow-primary/25'
                                            : 'border-border/90 bg-card text-foreground hover:border-primary/40 hover:bg-muted/60 hover:shadow-sm'
                                    "
                                    @click="emit('togglePreset', preset)"
                                >
                                    <span class="leading-tight">{{
                                        preset.label
                                    }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </CollapsibleContent>
        </Collapsible>

        <div class="mb-4 border-b border-border/60 pb-4">
            <h2
                class="text-[11px] font-bold tracking-[0.14em] text-muted-foreground uppercase"
            >
                Custom filters
            </h2>
            <p class="mt-1.5 text-sm text-muted-foreground">
                Mix and match fields, then apply.
            </p>
        </div>

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
        <div
            class="mt-8 flex flex-wrap items-center gap-3 border-t border-border/70 pt-6"
        >
            <Button
                class="min-h-10 min-w-[9.5rem] shadow-sm"
                @click="emit('applyFilters')"
            >
                Apply filters
            </Button>
            <Button
                variant="ghost"
                class="min-h-10 text-muted-foreground hover:text-foreground"
                @click="emit('clearFilters')"
            >
                Clear all
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
