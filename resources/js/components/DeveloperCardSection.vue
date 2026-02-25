<script setup lang="ts">
import { refDebounced } from '@vueuse/core';
import { Search, SlidersHorizontal } from 'lucide-vue-next';
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
    parseFiltersFromUrl,
    updateUrlWithFilters,
} from '@/lib/api';

const API_BASE = '/api/developers';
const initialFilters = parseFiltersFromUrl();
const searchQuery = ref(initialFilters.search ?? '');
const debouncedQuery = refDebounced(searchQuery, 500);
const filterJobTitle = ref<string[]>(initialFilters.jobTitle ?? []);
const filterSkill = ref<string[]>(initialFilters.skill ?? []);
const yearsMin = ref(initialFilters.yearsMin ?? '');
const yearsMax = ref(initialFilters.yearsMax ?? '');
const sortBy = ref(initialFilters.sort ?? 'name');
const advancedOpen = ref(false);
const developers = ref<Developer[]>([]);
const loading = ref(false);

const jobTitleSelectOpen = ref(false);
const skillSelectOpen = ref(false);

function onJobTitleOpenChange(open: boolean): void {
    jobTitleSelectOpen.value = open;
    if (open) skillSelectOpen.value = false;
}

function onSkillOpenChange(open: boolean): void {
    skillSelectOpen.value = open;
    if (open) jobTitleSelectOpen.value = false;
}

function getFilters(): DeveloperFilters {
    return {
        search: debouncedQuery.value,
        jobTitle: filterJobTitle.value,
        skill: filterSkill.value,
        yearsMin: yearsMin.value,
        yearsMax: yearsMax.value,
        sort: sortBy.value,
    };
}

async function fetchDevelopers(url?: string): Promise<void> {
    loading.value = true;
    try {
        const target = url ?? buildDevelopersApiUrl(API_BASE, getFilters());
        const res = await fetch(target);
        if (!res.ok) throw new Error('Failed to fetch developers');
        const data = await res.json();
        developers.value = data.data ?? [];
        updateUrlWithFilters(getFilters());
    } finally {
        loading.value = false;
    }
}

function applyFilters(): void {
    fetchDevelopers(buildDevelopersApiUrl(API_BASE, getFilters()));
}

function clearFilters(): void {
    searchQuery.value = '';
    filterJobTitle.value = [];
    filterSkill.value = [];
    yearsMin.value = '';
    yearsMax.value = '';
    sortBy.value = 'name';
    fetchDevelopers(API_BASE);
}

const sortOptions = [
    { value: 'name', label: 'Name' },
    { value: 'years_of_experience', label: 'Years of experience' },
    { value: '-created_at', label: 'Newest first' },
] as const;

const activeFilterCount = computed(() => {
    let count = 0;
    if (filterJobTitle.value.length > 0) count += filterJobTitle.value.length;
    if (filterSkill.value.length > 0) count += filterSkill.value.length;
    if (yearsMin.value) count++;
    if (yearsMax.value) count++;
    if (sortBy.value && sortBy.value !== 'name') count++;
    return count;
});

watch(debouncedQuery, () => {
    fetchDevelopers(buildDevelopersApiUrl(API_BASE, getFilters()));
}, { immediate: false });

watch(advancedOpen, (isOpen: boolean) => {
    if (!isOpen) {
        jobTitleSelectOpen.value = false;
        skillSelectOpen.value = false;
    }
});

onMounted(() => {
    fetchDevelopers();
});
</script>

<template>
    <section class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="sticky top-16 z-10 mb-6 flex flex-col gap-3 rounded-lg border border-border bg-background/95 px-3 py-2 backdrop-blur supports-[backdrop-filter]:bg-background/60 sm:flex-row sm:items-center">
            <div class="relative flex min-w-0 flex-1">
                <Search
                    class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                    aria-hidden="true"
                />
                <Input
                    v-model="searchQuery"
                    type="search"
                    placeholder="Search by name, email, or skills..."
                    class="h-10 flex-1 pl-9"
                    autocomplete="off"
                />
            </div>
            <Sheet v-model:open="advancedOpen">
                <SheetTrigger as-child>
                    <Button
                        variant="outline"
                        size="default"
                        class="relative h-10 shrink-0 gap-2"
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
                        <SheetTitle class="mb-4 text-lg font-semibold">
                            Advanced filters
                        </SheetTitle>
                        <SheetDescription class="sr-only">
                            Filter developers by job title, skills, years of experience, and sort order.
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
                            <div class="space-y-2">
                                <Label for="filter-sort">Sort by</Label>
                                <select
                                    id="filter-sort"
                                    v-model="sortBy"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                >
                                    <option
                                        v-for="opt in sortOptions"
                                        :key="opt.value"
                                        :value="opt.value"
                                    >
                                        {{ opt.label }}
                                    </option>
                                </select>
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
            class="rounded-lg border border-dashed border-border py-12 text-center text-muted-foreground"
        >
            No developers found.
        </div>
        <div
            v-else
            class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
        >
            <DeveloperCard
                v-for="developer in developers"
                :key="developer.id"
                :developer="developer"
            />
        </div>
    </section>
</template>
