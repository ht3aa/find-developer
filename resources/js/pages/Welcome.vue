<script setup lang="ts">
import { refDebounced, watchDebounced } from '@vueuse/core';
import { Head, router } from '@inertiajs/vue3';
import { Search, SlidersHorizontal } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import DeveloperCardSection from '@/components/DeveloperCardSection.vue';
import Navbar from '@/components/Navbar.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Sheet,
    SheetContent,
    SheetTrigger,
} from '@/components/ui/sheet';
import type { Developer } from '@/types/developer';

type Filters = {
    search?: string | null;
    name?: string | null;
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
        recommendedDeveloperCount?: number;
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
        recommendedDeveloperCount: 0,
        sort: 'name',
    },
);

const searchQuery = ref(props.filterSearch ?? props.filters?.search ?? '');
const filterName = ref(props.filters?.name ?? '');
const filterJobTitle = ref(props.filters?.['job_title.name'] ?? '');
const filterSkill = ref(props.filters?.skill ?? '');
const yearsMin = ref(props.filters?.years_min ?? '');
const yearsMax = ref(props.filters?.years_max ?? '');
const sortBy = ref(props.sort);
const advancedOpen = ref(false);
const debouncedQuery = refDebounced(searchQuery, 300);

function buildFilterUrl(overrides?: { search?: string }): string {
    const params = new URLSearchParams();
    const search = overrides?.search !== undefined ? overrides.search : searchQuery.value;
    if (search) params.set('filter[search]', search);
    if (filterName.value) params.set('filter[name]', filterName.value);
    if (filterJobTitle.value) params.set('filter[job_title.name]', filterJobTitle.value);
    if (filterSkill.value) params.set('filter[skill]', filterSkill.value);
    if (yearsMin.value) params.set('filter[years_min]', yearsMin.value);
    if (yearsMax.value) params.set('filter[years_max]', yearsMax.value);
    if (sortBy.value && sortBy.value !== 'name') params.set('sort', sortBy.value);
    const q = params.toString();
    return q ? `/?${q}` : '/';
}

function clearFilters(): void {
    advancedOpen.value = false;
    searchQuery.value = '';
    filterName.value = '';
    filterJobTitle.value = '';
    filterSkill.value = '';
    yearsMin.value = '';
    yearsMax.value = '';
    sortBy.value = 'name';
    router.get('/', {}, { preserveState: true, replace: true });
}

watch(debouncedQuery, () => {
    router.get(buildFilterUrl({ search: debouncedQuery.value }), {}, { preserveState: true, replace: true });
}, { immediate: false });

watchDebounced(
    () => [filterName.value, filterJobTitle.value, filterSkill.value, yearsMin.value, yearsMax.value, sortBy.value],
    () => {
        router.get(buildFilterUrl(), {}, { preserveState: true, replace: true });
    },
    { debounce: 300, deep: true },
);

watch(
    () => [props.filters, props.sort],
    () => {
        searchQuery.value = props.filterSearch ?? props.filters?.search ?? '';
        filterName.value = props.filters?.name ?? '';
        filterJobTitle.value = props.filters?.['job_title.name'] ?? '';
        filterSkill.value = props.filters?.skill ?? '';
        yearsMin.value = props.filters?.years_min ?? '';
        yearsMax.value = props.filters?.years_max ?? '';
        sortBy.value = props.sort ?? 'name';
    },
    { deep: true },
);

const sortOptions = [
    { value: 'name', label: 'Name' },
    { value: 'years_of_experience', label: 'Years of experience' },
    { value: '-created_at', label: 'Newest first' },
] as const;
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <!-- Sticky search bar + advanced filters -->
        <section
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
                        placeholder="Search developers by skill, e.g. Laravel, Vue, React..."
                        class="h-11 w-full border-0 bg-transparent pl-12 pr-4 text-base shadow-none placeholder:text-muted-foreground focus-visible:ring-0 sm:h-12 sm:pl-12"
                        autocomplete="off"
                    />
                </div>
                <Sheet v-model:open="advancedOpen">
                    <SheetTrigger as-child>
                        <Button
                            variant="outline"
                            size="default"
                            class="h-11 shrink-0 gap-2 sm:h-12"
                        >
                            <SlidersHorizontal class="h-4 w-4" aria-hidden="true" />
                            <span class="hidden sm:inline">Advanced filters</span>
                        </Button>
                    </SheetTrigger>
                    <SheetContent
                        side="top"
                        class="flex max-h-[85vh] flex-col overflow-y-auto border-b"
                    >
                        <div class="mx-auto w-full max-w-4xl py-6 pr-10">
                            <h2 class="mb-4 text-lg font-semibold">
                                Advanced filters
                            </h2>
                            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <div class="space-y-2">
                                    <Label for="filter-name">Name</Label>
                                    <Input
                                        id="filter-name"
                                        v-model="filterName"
                                        type="text"
                                        placeholder="Developer name"
                                        class="w-full"
                                        autocomplete="off"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <Label for="filter-job-title">Job title</Label>
                                    <Input
                                        id="filter-job-title"
                                        v-model="filterJobTitle"
                                        type="text"
                                        placeholder="e.g. Backend Developer"
                                        class="w-full"
                                        autocomplete="off"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <Label for="filter-skill">Skill</Label>
                                    <Input
                                        id="filter-skill"
                                        v-model="filterSkill"
                                        type="text"
                                        placeholder="e.g. Laravel, Vue"
                                        class="w-full"
                                        autocomplete="off"
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

        <!-- Developer counts -->
        <div class="flex w-full justify-center gap-3 px-4 py-4 sm:py-5">
            <div
                class="flex flex-col items-center gap-1 rounded-lg border border-border bg-card px-6 py-3 shadow-sm transition-colors hover:border-primary/30"
            >
                <span class="text-2xl font-bold text-primary">{{ developerCount }}</span>
                <span class="text-xs text-muted-foreground">
                    {{ developerCount === 1 ? 'developer' : 'developers' }}
                </span>
            </div>
            <div
                class="flex flex-col items-center gap-1 rounded-lg border border-border bg-card px-6 py-3 shadow-sm transition-colors hover:border-primary/30"
            >
                <span class="flex items-center gap-1.5">
                    <span class="size-2 rounded-full bg-primary" />
                    <span class="text-2xl font-bold text-primary">{{ recommendedDeveloperCount }}</span>
                </span>
                <span class="text-xs text-muted-foreground">recommended</span>
            </div>
        </div>

        <!-- Developers section -->
        <DeveloperCardSection
            :key="`${filterSearch ?? ''}-${sortBy}-${filterName}-${filterJobTitle}-${filterSkill}-${yearsMin}-${yearsMax}`"
            :developers="developers"
            :meta="meta"
            :links="links"
        />
    </div>
</template>
