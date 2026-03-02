<script setup lang="ts">
import { refDebounced, useClipboard } from '@vueuse/core';
import { Award, Check, Copy, FilterX, Search, SlidersHorizontal, Sparkles, Users } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import DeveloperCard from '@/components/DeveloperCard.vue';
import SearchableSelect from '@/components/SearchableSelect.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import type { Developer } from '@/types/developer';
import type { DeveloperFilters } from '@/lib/api';
import {
    buildDevelopersApiUrl,
    getFilteredPageUrl,
    parseFiltersFromUrl,
    updateUrlWithFilters,
} from '@/lib/api';
import { availabilityTypeOptions, hasUrlsOptions } from '@/utils/developerEnums';

const API_BASE = '/api/developers';
const initialFilters = parseFiltersFromUrl();
const searchQuery = ref(initialFilters.search ?? '');
const debouncedQuery = refDebounced(searchQuery, 500);
const filterJobTitle = ref<string[]>(initialFilters.jobTitle ?? []);
const filterSkill = ref<string[]>(initialFilters.skill ?? []);
const filterBadge = ref<string[]>(initialFilters.badge ?? []);
const filterAvailabilityType = ref<string[]>(initialFilters.availabilityType ?? []);
const filterHasUrls = ref<string[]>(initialFilters.hasUrls ?? []);
const isAvailable = ref(initialFilters.isAvailable ?? 'all');
const isRecommended = ref(initialFilters.isRecommended ?? 'all');
const yearsMin = ref(initialFilters.yearsMin ?? '');
const yearsMax = ref(initialFilters.yearsMax ?? '');
const advancedOpen = ref(false);
const developers = ref<Developer[]>([]);
const loading = ref(false);
const loadingMore = ref(false);
const nextPageUrl = ref<string | null>(null);

const stats = ref<{ total: number; recommended: number } | null>(null);
const paginationTotal = ref<number | null>(null);

const jobTitleSelectOpen = ref(false);
const skillSelectOpen = ref(false);
const badgeSelectOpen = ref(false);
const availabilityTypeSelectOpen = ref(false);
const hasUrlsSelectOpen = ref(false);

function onJobTitleOpenChange(open: boolean): void {
    jobTitleSelectOpen.value = open;
    if (open) {
        skillSelectOpen.value = false;
        badgeSelectOpen.value = false;
        availabilityTypeSelectOpen.value = false;
        hasUrlsSelectOpen.value = false;
    }
}

function onSkillOpenChange(open: boolean): void {
    skillSelectOpen.value = open;
    if (open) {
        jobTitleSelectOpen.value = false;
        badgeSelectOpen.value = false;
        availabilityTypeSelectOpen.value = false;
        hasUrlsSelectOpen.value = false;
    }
}

function onBadgeOpenChange(open: boolean): void {
    badgeSelectOpen.value = open;
    if (open) {
        jobTitleSelectOpen.value = false;
        skillSelectOpen.value = false;
        availabilityTypeSelectOpen.value = false;
        hasUrlsSelectOpen.value = false;
    }
}

function onAvailabilityTypeOpenChange(open: boolean): void {
    availabilityTypeSelectOpen.value = open;
    if (open) {
        jobTitleSelectOpen.value = false;
        skillSelectOpen.value = false;
        badgeSelectOpen.value = false;
        hasUrlsSelectOpen.value = false;
    }
}

function onHasUrlsOpenChange(open: boolean): void {
    hasUrlsSelectOpen.value = open;
    if (open) {
        jobTitleSelectOpen.value = false;
        skillSelectOpen.value = false;
        badgeSelectOpen.value = false;
        availabilityTypeSelectOpen.value = false;
    }
}

function getFilters(): DeveloperFilters {
    return {
        search: debouncedQuery.value,
        jobTitle: filterJobTitle.value,
        skill: filterSkill.value,
        badge: filterBadge.value,
        availabilityType: filterAvailabilityType.value,
        hasUrls: filterHasUrls.value,
        isAvailable: isAvailable.value,
        isRecommended: isRecommended.value,
        yearsMin: yearsMin.value,
        yearsMax: yearsMax.value,
    };
}

async function fetchDevelopers(url?: string, append = false): Promise<void> {
    if (append) {
        loadingMore.value = true;
    } else {
        loading.value = true;
    }
    try {
        const target = url ?? buildDevelopersApiUrl(API_BASE, getFilters());
        const res = await fetch(target);
        if (!res.ok) throw new Error('Failed to fetch developers');
        const data = await res.json();
        const newDevelopers = data.data ?? [];
        if (append) {
            developers.value = [...developers.value, ...newDevelopers];
        } else {
            developers.value = newDevelopers;
            if (data.total_developers !== undefined && data.recommended_developers !== undefined) {
                stats.value = { total: data.total_developers, recommended: data.recommended_developers };
            }
            if (data.meta?.total !== undefined) {
                paginationTotal.value = data.meta.total;
            }
        }
        nextPageUrl.value = data.links?.next ?? null;
        if (!append) {
            updateUrlWithFilters(getFilters());
        }
    } finally {
        loading.value = false;
        loadingMore.value = false;
    }
}

function loadMore(): void {
    if (nextPageUrl.value && !loadingMore.value) {
        fetchDevelopers(nextPageUrl.value, true);
    }
}

function applyFilters(): void {
    fetchDevelopers(buildDevelopersApiUrl(API_BASE, getFilters()));
}

function clearFilters(): void {
    searchQuery.value = '';
    filterJobTitle.value = [];
    filterSkill.value = [];
    filterBadge.value = [];
    filterAvailabilityType.value = [];
    filterHasUrls.value = [];
    isAvailable.value = 'all';
    isRecommended.value = 'all';
    yearsMin.value = '';
    yearsMax.value = '';
    fetchDevelopers(API_BASE);
}

const filteredPageUrl = computed(() => getFilteredPageUrl(getFilters()));

const aiPromptText = computed(() => {
    const url = filteredPageUrl.value;
    return `I need help finding the best developer match. Please open this URL and review the results:

${url}

This page shows developers already filtered by my criteria (e.g. skills, job title, availability, experience). Open the link, look at the listed developer profiles, and recommend the best match for my needs—or summarize the options so I can decide.`;
});

const { copy: copyToClipboard, copied: aiPromptCopied } = useClipboard({ copiedDuring: 2000 });

function copyAiPrompt(): void {
    copyToClipboard(aiPromptText.value);
}

const activeFilterCount = computed(() => {
    let count = 0;
    if (filterJobTitle.value.length > 0) count += filterJobTitle.value.length;
    if (filterSkill.value.length > 0) count += filterSkill.value.length;
    if (filterBadge.value.length > 0) count += filterBadge.value.length;
    if (filterAvailabilityType.value.length > 0) count += filterAvailabilityType.value.length;
    if (filterHasUrls.value.length > 0) count += filterHasUrls.value.length;
    if (isAvailable.value && isAvailable.value !== 'all') count++;
    if (isRecommended.value && isRecommended.value !== 'all') count++;
    if (yearsMin.value) count++;
    if (yearsMax.value) count++;
    return count;
});

watch(debouncedQuery, () => {
    fetchDevelopers(buildDevelopersApiUrl(API_BASE, getFilters()));
}, { immediate: false });

watch(advancedOpen, (isOpen: boolean) => {
    if (!isOpen) {
        jobTitleSelectOpen.value = false;
        skillSelectOpen.value = false;
        badgeSelectOpen.value = false;
        availabilityTypeSelectOpen.value = false;
        hasUrlsSelectOpen.value = false;
    }
});

onMounted(() => {
    fetchDevelopers();
});
</script>

<template>
    <section id="developers" class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div
            v-if="stats"
            class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6"
        >
            <div
                class="group relative flex items-center gap-4 overflow-hidden rounded-xl border border-border bg-card p-5 shadow-sm transition-all duration-200 hover:border-primary/30 hover:shadow-md sm:p-6"
            >
                <div
                    class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary transition-colors group-hover:bg-primary/15 sm:size-14"
                >
                    <Users class="size-6 sm:size-7" aria-hidden="true" />
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-2xl font-semibold tabular-nums tracking-tight text-foreground sm:text-3xl">
                        {{ stats.total }}
                    </p>
                    <p class="mt-0.5 text-sm text-muted-foreground">
                        Developers in the system
                    </p>
                </div>
            </div>
            <div
                class="group relative flex items-center gap-4 overflow-hidden rounded-xl border border-border bg-card p-5 shadow-sm transition-all duration-200 hover:border-primary/30 hover:shadow-md sm:p-6"
            >
                <div
                    class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600 transition-colors group-hover:bg-amber-500/15 dark:text-amber-400"
                >
                    <Award class="size-6 sm:size-7" aria-hidden="true" />
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-2xl font-semibold tabular-nums tracking-tight text-foreground sm:text-3xl">
                        {{ stats.recommended }}
                    </p>
                    <p class="mt-0.5 text-sm text-muted-foreground">
                        Recommended developers
                    </p>
                </div>
            </div>
        </div>
        <div class="sticky w-1/2 mx-auto top-18 z-sticky-bar mb-6 flex flex-col gap-3 rounded-lg border border-border bg-background/95 px-3 py-2 backdrop-blur supports-[backdrop-filter]:bg-background/60 sm:flex-row sm:items-center">
            <div class="relative flex min-w-0 flex-1 border border-primary rounded-md">
                <Search
                    class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                    aria-hidden="true"
                />
                <Input
                    v-model="searchQuery"
                    type="search"
                    placeholder="Search by name, email, or skills..."
                    class="h-9 flex-1 border border-input pl-9"
                    autocomplete="off"
                />
            </div>
            <p
                v-if="paginationTotal !== null"
                class="shrink-0 text-sm tabular-nums text-muted-foreground"
                aria-live="polite"
            >
                {{ paginationTotal }} developer{{ paginationTotal === 1 ? '' : 's' }}
            </p>
            <Sheet v-model:open="advancedOpen">
                <SheetTrigger as-child>
                    <Button
                        variant="outline"
                        size="default"
                        class="relative h-9 shrink-0 gap-2 border border-primary rounded-md"
                    >
                        <SlidersHorizontal class="h-4 w-4" aria-hidden="true" />
                        <span class="hidden sm:inline">Advanced filters</span>
                        <Badge
                            v-if="activeFilterCount > 0"
                            variant="secondary"
                            class="absolute -right-1 -top-1 flex size-5 items-center justify-center rounded-full p-0 text-xs"
                        >
                            {{ activeFilterCount }}
                        </Badge>
                    </Button>
                </SheetTrigger>
                <SheetContent
                    side="top"
                    class="flex max-h-[85vh] flex-col overflow-y-auto border-b"
                >
                    <div class="mx-auto w-full max-w-4xl py-6 pr-10">
                        <div class="mb-4 flex flex-wrap items-center gap-2 sm:gap-3">
                            <SheetTitle class="text-lg font-semibold">
                                Advanced filters
                            </SheetTitle>
                            <div
                                v-if="paginationTotal !== null"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-primary/20 bg-primary/10 px-3 py-1.5"
                                aria-live="polite"
                            >
                                <Users class="size-4 shrink-0 text-primary" aria-hidden="true" />
                                <span class="text-base font-semibold tabular-nums tracking-tight text-foreground">
                                    {{ paginationTotal }}
                                </span>
                                <span class="text-sm text-muted-foreground">
                                    developer{{ paginationTotal === 1 ? '' : 's' }}
                                </span>
                            </div>
                        </div>
                        <SheetDescription class="sr-only">
                            Filter developers by job title, skills, badges, availability type, has URLs, availability status, recommended status, and years of experience.
                        </SheetDescription>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="space-y-2">
                                <Label for="filter-job-title">Job title</Label>
                                <SearchableSelect
                                    id="filter-job-title"
                                    :model-value="filterJobTitle"
                                    :open="jobTitleSelectOpen"
                                    options-url="/api/job-titles"
                                    placeholder="e.g. Backend Developer"
                                    multiple
                                    :max-options="50"
                                    @update:model-value="filterJobTitle = Array.isArray($event) ? $event : ($event ? [$event] : [])"
                                    @update:open="onJobTitleOpenChange"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="filter-skill">Skill</Label>
                                <SearchableSelect
                                    id="filter-skill"
                                    :model-value="filterSkill"
                                    :open="skillSelectOpen"
                                    options-url="/api/skills"
                                    placeholder="e.g. Laravel, Vue"
                                    multiple
                                    :max-options="50"
                                    @update:model-value="filterSkill = Array.isArray($event) ? $event : ($event ? [$event] : [])"
                                    @update:open="onSkillOpenChange"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="filter-badge">Badge</Label>
                                <SearchableSelect
                                    id="filter-badge"
                                    :model-value="filterBadge"
                                    :open="badgeSelectOpen"
                                    options-url="/api/badges"
                                    placeholder="e.g. Laravel Expert"
                                    multiple
                                    :max-options="50"
                                    @update:model-value="filterBadge = Array.isArray($event) ? $event : ($event ? [$event] : [])"
                                    @update:open="onBadgeOpenChange"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="filter-availability-type">Availability type</Label>
                                <SearchableSelect
                                    id="filter-availability-type"
                                    :model-value="filterAvailabilityType"
                                    :open="availabilityTypeSelectOpen"
                                    :options="availabilityTypeOptions"
                                    placeholder="e.g. Full-time, Remote"
                                    multiple
                                    @update:model-value="filterAvailabilityType = Array.isArray($event) ? $event : ($event ? [$event] : [])"
                                    @update:open="onAvailabilityTypeOpenChange"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="filter-has-urls">Has URLs</Label>
                                <SearchableSelect
                                    id="filter-has-urls"
                                    :model-value="filterHasUrls"
                                    :open="hasUrlsSelectOpen"
                                    :options="hasUrlsOptions"
                                    placeholder="e.g. GitHub, LinkedIn"
                                    multiple
                                    @update:model-value="filterHasUrls = Array.isArray($event) ? $event : ($event ? [$event] : [])"
                                    @update:open="onHasUrlsOpenChange"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="filter-is-available">Availability</Label>
                                <select
                                    id="filter-is-available"
                                    v-model="isAvailable"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
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
                                    v-model="isRecommended"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
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
                                    v-model="yearsMin"
                                    type="number"
                                    min="0"
                                    placeholder="0"
                                    class="w-full"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="filter-years-max">Max. years of experience</Label>
                                <Input
                                    id="filter-years-max"
                                    v-model="yearsMax"
                                    type="number"
                                    min="0"
                                    placeholder="Any"
                                    class="w-full"
                                />
                            </div>
                        </div>
                        <div class="mt-6 flex flex-wrap items-center gap-2">
                            <Button @click="applyFilters">
                                Apply filters
                            </Button>
                            <Button
                                variant="ghost"
                                class="text-muted-foreground"
                                @click="clearFilters"
                            >
                                Clear all
                            </Button>
                        </div>

                        <div
                            class="mt-6 overflow-hidden rounded-xl border border-border bg-card shadow-sm transition-all duration-200"
                        >
                            <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-start sm:justify-between sm:gap-6">
                                <div class="flex min-w-0 flex-1 items-start gap-3">
                                    <div
                                        class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                                    >
                                        <Sparkles class="size-5" aria-hidden="true" />
                                    </div>
                                    <div class="min-w-0 flex-1 space-y-1">
                                        <Label class="text-base font-semibold tracking-tight text-foreground">
                                            AI prompt
                                        </Label>
                                        <p class="text-sm text-muted-foreground">
                                            Copy this text and share it with an AI assistant so it can search this site using your current filters and find the best match.
                                        </p>
                                    </div>
                                </div>
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="default"
                                    class="shrink-0 gap-2 border-primary"
                                    :aria-label="aiPromptCopied ? 'Copied' : 'Copy AI prompt'"
                                    @click="copyAiPrompt"
                                >
                                    <Check
                                        v-if="aiPromptCopied"
                                        class="size-4 text-green-600 dark:text-green-400"
                                        aria-hidden="true"
                                    />
                                    <Copy v-else class="size-4" aria-hidden="true" />
                                    <span>{{ aiPromptCopied ? 'Copied' : 'Copy' }}</span>
                                </Button>
                            </div>
                            <div class="border-t border-border bg-muted/20 px-4 py-3 sm:px-4 sm:py-3">
                                <textarea
                                    :value="aiPromptText"
                                    readonly
                                    rows="6"
                                    class="w-full resize-none rounded-lg border border-border/60 bg-background px-3.5 py-3 text-sm font-mono leading-relaxed text-foreground shadow-inner selection:bg-primary/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/20"
                                    aria-label="AI prompt text"
                                />
                            </div>
                        </div>
                    </div>
                </SheetContent>
            </Sheet>
        </div>

        <div
            v-if="loading"
            class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
        >
            <div
                v-for="i in 6"
                :key="i"
                class="h-64 animate-pulse rounded-lg border border-border bg-muted/50"
            />
        </div>
        <div
            v-else-if="developers.length === 0"
            class="flex flex-col items-center gap-4 rounded-lg border border-dashed border-border py-12 text-center text-muted-foreground"
        >
            <p>No developers found.</p>
            <Button
                variant="outline"
                size="sm"
                class="gap-2"
                @click="clearFilters"
            >
                <FilterX class="h-4 w-4" aria-hidden="true" />
                Clear filters
            </Button>
        </div>
        <template v-else>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <DeveloperCard
                    v-for="developer in developers"
                    :key="developer.id"
                    :developer="developer"
                />
            </div>
            <div
                v-if="nextPageUrl && developers.length > 0"
                class="mt-8 flex justify-center"
            >
                <Button
                    variant="default"
                    size="default"
                    :disabled="loadingMore"
                    @click="loadMore"
                >
                    <span v-if="loadingMore">Loading...</span>
                    <span v-else>Load more</span>
                </Button>
            </div>
        </template>
    </section>
</template>
