<script setup lang="ts">
import { refDebounced, watchDebounced } from '@vueuse/core';
import { Head, router } from '@inertiajs/vue3';
import { Search, SlidersHorizontal } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import DeveloperCardSection from '@/components/DeveloperCardSection.vue';
import Footer from '@/components/Footer.vue';
import Hero from '@/components/Hero.vue';
import Navbar from '@/components/Navbar.vue';
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

type Filters = {
    search?: string | null;
    'job_title.name'?: string | null;
    skill?: string | null;
    years_min?: string | null;
    years_max?: string | null;
};

const props = withDefaults(
    defineProps<{
        canRegister?: boolean;
        developerCount?: number;
        developers: Developer[];
        filterSearch?: string | null;
        filters?: Filters;
        jobTitles?: { value: string; label: string }[];
        recommendedDeveloperCount?: number;
        skills?: { value: string; label: string }[];
        sort?: string;
        meta: {
            current_page: number;
            last_page: number;
            per_page: number;
            total: number;
            from: number | null;
            to: number | null;
        };
        links: {
            prev: string | null;
            next: string | null;
        };
    }>(),
    {
        developerCount: 0,
        filters: () => ({}),
        jobTitles: () => [],
        recommendedDeveloperCount: 0,
        skills: () => [],
        sort: 'name',
    },
);

const searchQuery = ref(props.filterSearch ?? props.filters?.search ?? '');
function parseFilterArray(val: string | string[] | null | undefined): string[] {
    if (val == null || val === '') return [];
    if (Array.isArray(val)) return val.filter(Boolean);
    return val.includes(',') ? val.split(',').map((s) => s.trim()).filter(Boolean) : [val];
}

const filterJobTitle = ref<string | string[]>(parseFilterArray(props.filters?.['job_title.name']));
const filterSkill = ref<string | string[]>(parseFilterArray(props.filters?.skill));
const yearsMin = ref(props.filters?.years_min ?? '');
const yearsMax = ref(props.filters?.years_max ?? '');
const sortBy = ref(props.sort);
const advancedOpen = ref(false);
const debouncedQuery = refDebounced(searchQuery, 300);

// Ensure only one SearchableSelect is open at a time to prevent interaction conflicts
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

function toFilterValue(val: string | string[] | null | undefined): string {
    if (val == null) return '';
    return Array.isArray(val) ? val.filter(Boolean).join(',') : String(val);
}

function buildFilterUrl(overrides?: { search?: string }): string {
    const params = new URLSearchParams();
    const search = overrides?.search !== undefined ? overrides.search : searchQuery.value;
    if (search) params.set('filter[search]', search);
    const jobTitleVal = toFilterValue(filterJobTitle.value);
    if (jobTitleVal) params.set('filter[job_title.name]', jobTitleVal);
    const skillVal = toFilterValue(filterSkill.value);
    if (skillVal) params.set('filter[skill]', skillVal);
    if (yearsMin.value) params.set('filter[years_min]', yearsMin.value);
    if (yearsMax.value) params.set('filter[years_max]', yearsMax.value);
    if (sortBy.value && sortBy.value !== 'name') params.set('sort', sortBy.value);
    const q = params.toString();
    return q ? `/?${q}` : '/';
}

function clearFilters(): void {
    advancedOpen.value = false;
    searchQuery.value = '';
    filterJobTitle.value = [];
    filterSkill.value = [];
    yearsMin.value = '';
    yearsMax.value = '';
    sortBy.value = 'name';
    router.get('/', {}, { preserveState: true, preserveScroll: true, replace: true });
}

watch(debouncedQuery, () => {
    router.get(buildFilterUrl({ search: debouncedQuery.value }), {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, { immediate: false });

watchDebounced(
    () => [filterJobTitle.value, filterSkill.value, yearsMin.value, yearsMax.value, sortBy.value],
    () => {
        router.get(buildFilterUrl(), {}, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    },
    { debounce: 150, deep: true },
);

watch(
    () => [props.filters, props.sort],
    () => {
        searchQuery.value = props.filterSearch ?? props.filters?.search ?? '';
        filterJobTitle.value = parseFilterArray(props.filters?.['job_title.name']);
        filterSkill.value = parseFilterArray(props.filters?.skill);
        yearsMin.value = props.filters?.years_min ?? '';
        yearsMax.value = props.filters?.years_max ?? '';
        sortBy.value = props.sort ?? 'name';
    },
    { deep: true },
);

watch(advancedOpen, (isOpen: boolean) => {
    if (!isOpen) {
        jobTitleSelectOpen.value = false;
        skillSelectOpen.value = false;
    }
});

const sortOptions = [
    { value: 'name', label: 'Name' },
    { value: 'years_of_experience', label: 'Years of experience' },
    { value: '-created_at', label: 'Newest first' },
] as const;

const activeFilterCount = computed(() => {
    let count = 0;
    const jt = filterJobTitle.value;
    const sk = filterSkill.value;
    if (Array.isArray(jt) ? jt.length > 0 : jt) count += Array.isArray(jt) ? jt.length : 1;
    if (Array.isArray(sk) ? sk.length > 0 : sk) count += Array.isArray(sk) ? sk.length : 1;
    if (yearsMin.value) count++;
    if (yearsMax.value) count++;
    if (sortBy.value && sortBy.value !== 'name') count++;
    return count;
});

const hasActiveFilters = computed(() => activeFilterCount.value > 0);
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <Hero
            :can-register="canRegister"
            badge="Find developers"
            title="Find the right developer for your project"
            description="Browse vetted developers, filter by skills and experience, and connect with the best match for your team."
            primary-action-label="Browse developers"
            secondary-action-label="Sign up"
        />

        <!-- Sticky search bar + advanced filters -->
        <section
            data-hero-search
            class="sticky top-16 z-sticky-bar flex w-full flex-col items-center border-b border-border bg-background/95 px-4 py-4 backdrop-blur supports-[backdrop-filter]:bg-background/60 sm:py-5 lg:pt-6 lg:pb-5"
        >
            <div class="flex w-full max-w-3xl items-center gap-3">
                <div
                    class="relative flex min-w-0 flex-1 items-center rounded-lg border border-border bg-card shadow-sm transition-colors focus-within:border-ring focus-within:ring-2 focus-within:ring-ring/20"
                >
                    <Search
                        class="pointer-events-none absolute left-4 h-5 w-5 shrink-0 text-muted-foreground"
                        aria-hidden="true"
                    />
                    <Input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Search by name, email, or skills..."
                        class="h-11 w-full border-0 bg-transparent pl-12 pr-4 text-base shadow-none placeholder:text-muted-foreground focus-visible:ring-0 sm:h-12 sm:pl-12"
                        autocomplete="off"
                    />
                </div>
                <Sheet v-model:open="advancedOpen">
                    <SheetTrigger as-child>
                        <Button
                            variant="outline"
                            size="default"
                            class="relative h-11 shrink-0 gap-2 sm:h-12"
                        >
                            <SlidersHorizontal class="h-4 w-4" aria-hidden="true" />
                            <span class="hidden sm:inline">Advanced filters</span>
                            <Badge
                                v-if="hasActiveFilters"
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
                                        :options="jobTitles"
                                        placeholder="e.g. Backend Developer"
                                        multiple
                                        :max-options="50"
                                        @update:model-value="filterJobTitle = $event ?? []"
                                        @update:open="onJobTitleOpenChange"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <Label for="filter-skill">Skill</Label>
                                    <SearchableSelect
                                        id="filter-skill"
                                        :model-value="filterSkill"
                                        :open="skillSelectOpen"
                                        :options="skills"
                                        placeholder="e.g. Laravel, Vue"
                                        multiple
                                        :max-options="50"
                                        @update:model-value="filterSkill = $event ?? []"
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
        </section>

         <!-- Developers section -->
        <DeveloperCardSection
            :key="`${filterSearch ?? ''}-${sortBy}-${filterJobTitle}-${filterSkill}-${yearsMin}-${yearsMax}`"
            :developers="developers"
            :meta="meta"
            :links="links"
        />

        <Footer />
    </div>
</template>
