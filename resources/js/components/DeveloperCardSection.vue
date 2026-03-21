<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { refDebounced, useClipboard } from '@vueuse/core';
import {
    Award,
    Check,
    ExternalLink,
    FileText,
    FilterX,
    LayoutGrid,
    Scale,
    Search,
    Send,
    SlidersHorizontal,
    Star,
    Table2,
    Users,
} from 'lucide-vue-next';
import {
    computed,
    defineAsyncComponent,
    onMounted,
    ref,
    watch,
} from 'vue';
import DeveloperCard from '@/components/DeveloperCard.vue';

const DeveloperCompareDialog = defineAsyncComponent(
    () => import('@/components/DeveloperCompareDialog.vue'),
);
const DeveloperOfferForm = defineAsyncComponent(
    () => import('@/components/DeveloperOfferForm.vue'),
);
const DeveloperFiltersPanelContent = defineAsyncComponent(
    () => import('@/components/DeveloperFiltersPanelContent.vue'),
);
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import type { DeveloperFilters } from '@/lib/api';
import {
    buildDevelopersApiUrl,
    getFilteredApiUrl,
    getFilteredPageUrl,
    parseFiltersFromUrl,
    updateUrlWithFilters,
} from '@/lib/api';
import type { Developer } from '@/types/developer';

const props = withDefaults(
    defineProps<{
        /** When set, restricts results to these developer IDs (e.g. hackathon subscribers). */
        developerIds?: number[];
        /** URL to submit developer offers (when set, enables selection + send offer). */
        developerOffersStoreUrl?: string;
    }>(),
    {},
);

const offerFormOpen = ref(false);

const compareIds = ref<number[]>([]);
const compareDevelopersData = ref<Developer[]>([]);
const compareDialogOpen = ref(false);

const canSelectDevelopers = computed(() => !!props.developerOffersStoreUrl);

function developerNumericId(developer: Pick<Developer, 'id'>): number {
    return Number(developer.id);
}

function onCardSelectionChange(
    rawId: number | string,
    selected: boolean,
): void {
    const id = Number(rawId);
    if (Number.isNaN(id)) {
        return;
    }
    if (selected) {
        if (!compareIds.value.includes(id)) {
            const dev = developers.value.find(
                (d) => developerNumericId(d) === id,
            );
            if (dev) {
                compareIds.value = [...compareIds.value, id];
                compareDevelopersData.value = [
                    ...compareDevelopersData.value,
                    dev,
                ];
            }
        }
    } else {
        compareIds.value = compareIds.value.filter((i) => i !== id);
        compareDevelopersData.value = compareDevelopersData.value.filter(
            (d) => developerNumericId(d) !== id,
        );
    }
}

function onTableCheckboxUpdate(
    developer: Developer,
    value: boolean | 'indeterminate',
): void {
    onCardSelectionChange(developer.id, value === true);
}

function clearSelection(): void {
    compareIds.value = [];
    compareDevelopersData.value = [];
}

function openOfferForm(): void {
    if (compareIds.value.length > 0 && canSelectDevelopers.value) {
        offerFormOpen.value = true;
    }
}

function selectAllCurrent(): void {
    const ids = developers.value.map((d) => developerNumericId(d));
    compareIds.value = ids;
    compareDevelopersData.value = developers.value.filter((d) =>
        ids.includes(developerNumericId(d)),
    );
}

const allCurrentSelected = computed(
    () =>
        developers.value.length > 0 &&
        developers.value.every((d) =>
            compareIds.value.includes(developerNumericId(d)),
        ),
);

function onOfferSuccess(): void {
    offerFormOpen.value = false;
    clearSelection();
}

const compareDevelopers = computed((): [Developer, Developer] | null => {
    if (compareDevelopersData.value.length !== 2) return null;
    return [compareDevelopersData.value[0], compareDevelopersData.value[1]] as [
        Developer,
        Developer,
    ];
});

function openCompareDialog(): void {
    if (compareDevelopers.value) {
        compareDialogOpen.value = true;
    }
}

function clearCompare(): void {
    compareIds.value = [];
    compareDevelopersData.value = [];
    compareDialogOpen.value = false;
}

const VIEW_LAYOUT_STORAGE_KEY = 'developers-directory-view-layout';

type ViewLayout = 'cards' | 'table';

const viewLayout = ref<ViewLayout>('cards');

const API_BASE = '/api/developers';
const initialFilters = parseFiltersFromUrl();
const searchQuery = ref(initialFilters.search ?? '');
const debouncedQuery = refDebounced(searchQuery, 500);
const filterJobTitle = ref<string[]>(initialFilters.jobTitle ?? []);
const filterSkill = ref<string[]>(initialFilters.skill ?? []);
const filterBadge = ref<string[]>(initialFilters.badge ?? []);
const filterAvailabilityType = ref<string[]>(
    initialFilters.availabilityType ?? [],
);
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
        ids: props.developerIds?.length ? props.developerIds : undefined,
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
            if (
                data.total_developers !== undefined &&
                data.recommended_developers !== undefined
            ) {
                stats.value = {
                    total: data.total_developers,
                    recommended: data.recommended_developers,
                };
            }
            if (data.meta?.total !== undefined) {
                paginationTotal.value = data.meta.total;
            }
        }
        nextPageUrl.value = data.links?.next ?? null;
        if (!append && !props.developerIds?.length) {
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
    fetchDevelopers(
        props.developerIds?.length
            ? buildDevelopersApiUrl(API_BASE, { ids: props.developerIds })
            : API_BASE,
    );
}

const filteredPageUrl = computed(() => getFilteredPageUrl(getFilters()));
const filteredApiUrl = computed(() => getFilteredApiUrl(getFilters()));

const aiPromptText = computed(() => {
    const apiUrl = filteredApiUrl.value;
    const pageUrl = filteredPageUrl.value;
    return `I need help finding the best developer match. To get the actual list of developers (not just an empty page), use this data URL:

${apiUrl}

Instructions:
1. Send a GET request to the URL above. It returns JSON (not HTML).
2. The response has a "data" array: each item is a developer profile (name, skills, job title, bio, links, etc.).
3. Use that data to recommend the best match for my needs, or summarize the options so I can decide.

Filters are already applied in the URL (e.g. job title, skills, availability). For human browsing you can also open: ${pageUrl}`;
});

const { copy: copyToClipboard, copied: aiPromptCopied } = useClipboard({
    copiedDuring: 2000,
});

function copyAiPrompt(): void {
    copyToClipboard(aiPromptText.value);
}

const activeFilterCount = computed(() => {
    let count = 0;
    if (filterJobTitle.value.length > 0) count += filterJobTitle.value.length;
    if (filterSkill.value.length > 0) count += filterSkill.value.length;
    if (filterBadge.value.length > 0) count += filterBadge.value.length;
    if (filterAvailabilityType.value.length > 0)
        count += filterAvailabilityType.value.length;
    if (filterHasUrls.value.length > 0) count += filterHasUrls.value.length;
    if (isAvailable.value && isAvailable.value !== 'all') count++;
    if (isRecommended.value && isRecommended.value !== 'all') count++;
    if (yearsMin.value) count++;
    if (yearsMax.value) count++;
    return count;
});

watch(
    debouncedQuery,
    () => {
        fetchDevelopers(buildDevelopersApiUrl(API_BASE, getFilters()));
    },
    { immediate: false },
);

watch(advancedOpen, (isOpen: boolean) => {
    if (!isOpen) {
        jobTitleSelectOpen.value = false;
        skillSelectOpen.value = false;
        badgeSelectOpen.value = false;
        availabilityTypeSelectOpen.value = false;
        hasUrlsSelectOpen.value = false;
    }
});

function developerProfileHref(developer: Developer): string | null {
    if (developer.profile_url) {
        return developer.profile_url;
    }
    if (developer.slug) {
        return `/developers/${developer.slug}`;
    }
    return null;
}

function developerTableSalaryLabel(developer: Developer): string {
    const from = developer.expected_salary_from;
    const to = developer.expected_salary_to;
    const cur = developer.currency?.trim() ? developer.currency : '';
    const curSuffix = cur ? ` ${cur}` : '';

    if (from != null && to != null) {
        return `${from} – ${to}${curSuffix}`;
    }
    if (from != null) {
        return `From ${from}${curSuffix}`;
    }
    if (to != null) {
        return `Up to ${to}${curSuffix}`;
    }

    return '—';
}

function availabilityTypeLabels(developer: Developer): string {
    const labels =
        developer.availability_type
            ?.map((t) => t.label)
            .filter((label) => label.length > 0) ?? [];
    return labels.join(', ');
}

function visibleSkillTags(developer: Developer): {
    tags: string[];
    more: number;
} {
    const max = 4;
    const names = developer.skills.map((s) => s.name);
    if (names.length <= max) {
        return { tags: names, more: 0 };
    }
    return { tags: names.slice(0, max), more: names.length - max };
}

onMounted(() => {
    if (typeof localStorage !== 'undefined') {
        const stored = localStorage.getItem(VIEW_LAYOUT_STORAGE_KEY);
        if (stored === 'cards' || stored === 'table') {
            viewLayout.value = stored;
        }
    }
    fetchDevelopers();
});

watch(viewLayout, (layout: ViewLayout) => {
    if (typeof localStorage !== 'undefined') {
        localStorage.setItem(VIEW_LAYOUT_STORAGE_KEY, layout);
    }
});
</script>

<template>
    <section
        id="developers"
        class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8"
    >
        <!-- Subscribe note -->
        <div
            class="mb-8 rounded-xl border-2 border-primary/30 bg-primary/10 p-6 text-center sm:p-8"
        >
            <p class="text-lg font-semibold text-foreground sm:text-xl">
                Unlock direct contact details & CVs
            </p>
            <p class="mt-2 text-sm text-muted-foreground sm:text-base">
                Subscribers get access to developer phone numbers and resumes —
                connect faster with the right talent for your team.
            </p>
            <a
                href="mailto:ht3aa2001@gmail.com?subject=Subscription%20Inquiry%20-%20Phone%20%26%20CV"
                target="_blank"
                rel="noopener noreferrer"
                class="mt-4 inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground shadow-sm transition-colors hover:bg-primary/90"
            >
                Get access — contact ht3aa2001@gmail.com
            </a>
        </div>

        <div
            v-if="stats && !developerIds?.length"
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
                    <p
                        class="text-2xl font-semibold tracking-tight text-foreground tabular-nums sm:text-3xl"
                    >
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
                    <p
                        class="text-2xl font-semibold tracking-tight text-foreground tabular-nums sm:text-3xl"
                    >
                        {{ stats.recommended }}
                    </p>
                    <p class="mt-0.5 text-sm text-muted-foreground">
                        Recommended developers
                    </p>
                </div>
            </div>
        </div>
        <div
            class="z-sticky-bar sticky top-18 mb-6 rounded-xl border border-border bg-card/95 shadow-sm backdrop-blur-md supports-[backdrop-filter]:bg-card/80"
        >
            <!-- Search row: primary focus -->
            <div
                class="flex flex-col gap-3 px-4 py-3 sm:flex-row sm:items-center sm:gap-4 sm:px-5 sm:py-3.5"
            >
                <div
                    class="relative flex min-w-0 flex-1 items-center rounded-lg border border-input bg-muted/30 transition-colors focus-within:border-primary focus-within:bg-background focus-within:ring-2 focus-within:ring-primary/20"
                >
                    <Search
                        class="pointer-events-none absolute left-3.5 h-4 w-4 shrink-0 text-muted-foreground"
                        aria-hidden="true"
                    />
                    <Input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Search by name, email, or skills..."
                        class="h-10 min-w-0 flex-1 border-0 bg-transparent pr-4 pl-10 shadow-none focus-visible:ring-0"
                        autocomplete="off"
                    />
                </div>

                <!-- Results count + actions group -->
                <div
                    class="flex flex-wrap items-center gap-2 sm:gap-3 sm:border-l sm:border-border sm:pl-4"
                >
                    <p
                        v-if="paginationTotal !== null"
                        class="order-first shrink-0 text-sm font-medium text-muted-foreground tabular-nums sm:order-none"
                        aria-live="polite"
                    >
                        <span class="text-foreground">{{
                            paginationTotal
                        }}</span>
                        {{ paginationTotal === 1 ? 'developer' : 'developers' }}
                    </p>

                    <div
                        class="inline-flex shrink-0 rounded-lg border border-border/80 bg-muted/40 p-0.5 shadow-inner"
                        role="group"
                        aria-label="Developer list layout"
                    >
                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            :class="[
                                'h-8 gap-1.5 rounded-md px-2.5 sm:px-3',
                                viewLayout === 'cards'
                                    ? 'bg-background text-foreground shadow-sm hover:bg-background'
                                    : 'text-muted-foreground hover:bg-transparent hover:text-foreground',
                            ]"
                            :aria-pressed="viewLayout === 'cards'"
                            @click="viewLayout = 'cards'"
                        >
                            <LayoutGrid
                                class="size-4 shrink-0"
                                aria-hidden="true"
                            />
                            <span class="hidden sm:inline">Cards</span>
                        </Button>
                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            :class="[
                                'h-8 gap-1.5 rounded-md px-2.5 sm:px-3',
                                viewLayout === 'table'
                                    ? 'bg-background text-foreground shadow-sm hover:bg-background'
                                    : 'text-muted-foreground hover:bg-transparent hover:text-foreground',
                            ]"
                            :aria-pressed="viewLayout === 'table'"
                            @click="viewLayout = 'table'"
                        >
                            <Table2 class="size-4 shrink-0" aria-hidden="true" />
                            <span class="hidden sm:inline">Table</span>
                        </Button>
                    </div>

                    <!-- Compare (always visible) -->
                    <div class="flex items-center gap-1.5">
                        <Button
                            v-if="compareIds.length > 0"
                            variant="ghost"
                            size="sm"
                            class="h-8 gap-1.5 px-2.5 text-muted-foreground hover:bg-muted/60 hover:text-foreground"
                            @click="clearCompare"
                        >
                            <span class="tabular-nums"
                                >{{ compareIds.length }}/2</span
                            >
                            Clear
                        </Button>
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <span class="inline-flex">
                                        <Button
                                            :variant="
                                                compareIds.length === 2
                                                    ? 'default'
                                                    : 'outline'
                                            "
                                            size="sm"
                                            class="h-8 shrink-0 gap-1.5"
                                            :disabled="compareIds.length !== 2"
                                            @click="openCompareDialog"
                                        >
                                            <Scale
                                                class="size-4"
                                                aria-hidden="true"
                                            />
                                            Compare
                                        </Button>
                                    </span>
                                </TooltipTrigger>
                                <TooltipContent>
                                    {{
                                        compareIds.length > 2
                                            ? 'Compare only works for 2 selections'
                                            : compareIds.length === 2
                                              ? 'Compare selected developers'
                                              : 'Select 2 developers to compare'
                                    }}
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </div>

                    <template v-if="canSelectDevelopers">
                        <div
                            class="hidden h-4 w-px shrink-0 bg-border sm:block"
                            aria-hidden="true"
                        />
                        <Button
                            variant="outline"
                            size="sm"
                            class="h-8 shrink-0 gap-1.5"
                            @click="
                                allCurrentSelected
                                    ? clearSelection()
                                    : selectAllCurrent()
                            "
                        >
                            <Check
                                v-if="allCurrentSelected"
                                class="size-4"
                                aria-hidden="true"
                            />
                            {{
                                allCurrentSelected
                                    ? 'Deselect all'
                                    : 'Select all'
                            }}
                        </Button>
                        <Button
                            :variant="
                                compareIds.length > 0 ? 'default' : 'outline'
                            "
                            size="sm"
                            class="h-8 shrink-0 gap-1.5"
                            :disabled="compareIds.length === 0"
                            @click="openOfferForm"
                        >
                            <Send class="size-4" aria-hidden="true" />
                            Send offer
                            <span v-if="compareIds.length > 0">
                                ({{ compareIds.length }})
                            </span>
                        </Button>
                    </template>

                    <!-- Advanced filters -->
                    <div
                        class="hidden h-4 w-px shrink-0 bg-border sm:block"
                        aria-hidden="true"
                    />
                    <Sheet v-model:open="advancedOpen">
                        <SheetTrigger as-child>
                            <Button
                                variant="outline"
                                size="sm"
                                class="relative h-8 shrink-0 gap-1.5 border-primary/50 bg-primary/5 hover:border-primary hover:bg-primary/10"
                            >
                                <SlidersHorizontal
                                    class="size-4"
                                    aria-hidden="true"
                                />
                                <span class="hidden sm:inline">Filters</span>
                                <Badge
                                    v-if="activeFilterCount > 0"
                                    variant="secondary"
                                    class="absolute -top-1 -right-1 flex size-5 min-w-5 items-center justify-center rounded-full px-1 text-[10px] font-semibold"
                                >
                                    {{ activeFilterCount }}
                                </Badge>
                            </Button>
                        </SheetTrigger>
                        <SheetContent
                            side="top"
                            class="flex max-h-[85vh] flex-col overflow-y-auto border-b"
                        >
                            <Suspense>
                                <DeveloperFiltersPanelContent
                                    v-if="advancedOpen"
                                    :filter-job-title="filterJobTitle"
                                    :filter-skill="filterSkill"
                                    :filter-badge="filterBadge"
                                    :filter-availability-type="
                                        filterAvailabilityType
                                    "
                                    :filter-has-urls="filterHasUrls"
                                    :is-available="isAvailable"
                                    :is-recommended="isRecommended"
                                    :years-min="yearsMin"
                                    :years-max="yearsMax"
                                    :job-title-select-open="jobTitleSelectOpen"
                                    :skill-select-open="skillSelectOpen"
                                    :badge-select-open="badgeSelectOpen"
                                    :availability-type-select-open="
                                        availabilityTypeSelectOpen
                                    "
                                    :has-urls-select-open="hasUrlsSelectOpen"
                                    :pagination-total="paginationTotal"
                                    :ai-prompt-text="aiPromptText"
                                    :ai-prompt-copied="aiPromptCopied"
                                    @update:filter-job-title="
                                        filterJobTitle = $event
                                    "
                                    @update:filter-skill="filterSkill = $event"
                                    @update:filter-badge="filterBadge = $event"
                                    @update:filter-availability-type="
                                        filterAvailabilityType = $event
                                    "
                                    @update:filter-has-urls="
                                        filterHasUrls = $event
                                    "
                                    @update:is-available="isAvailable = $event"
                                    @update:is-recommended="
                                        isRecommended = $event
                                    "
                                    @update:years-min="yearsMin = $event"
                                    @update:years-max="yearsMax = $event"
                                    @update:job-title-select-open="
                                        onJobTitleOpenChange($event)
                                    "
                                    @update:skill-select-open="
                                        onSkillOpenChange($event)
                                    "
                                    @update:badge-select-open="
                                        onBadgeOpenChange($event)
                                    "
                                    @update:availability-type-select-open="
                                        onAvailabilityTypeOpenChange($event)
                                    "
                                    @update:has-urls-select-open="
                                        onHasUrlsOpenChange($event)
                                    "
                                    @apply-filters="applyFilters"
                                    @clear-filters="clearFilters"
                                    @copy-ai-prompt="copyAiPrompt"
                                />
                                <template #fallback>
                                    <div
                                        class="flex min-h-[200px] items-center justify-center py-12"
                                    >
                                        <div
                                            class="h-8 w-8 animate-spin rounded-full border-2 border-primary border-t-transparent"
                                            aria-hidden
                                        />
                                    </div>
                                </template>
                            </Suspense>
                        </SheetContent>
                    </Sheet>
                </div>
            </div>
        </div>

        <div
            v-if="loading && viewLayout === 'cards'"
            class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
        >
            <div
                v-for="i in 6"
                :key="i"
                class="h-64 animate-pulse rounded-lg border border-border bg-muted/50"
            />
        </div>
        <div
            v-else-if="loading && viewLayout === 'table'"
            class="overflow-hidden rounded-2xl border border-border/80 bg-card shadow-sm"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-[86rem] caption-bottom text-sm">
                    <thead
                        class="border-b border-border/70 bg-gradient-to-b from-muted/50 to-muted/25"
                    >
                        <tr>
                            <th
                                class="h-11 w-12 px-4 text-left align-middle"
                                scope="col"
                            />
                            <th
                                class="h-11 px-4 text-left align-middle text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                scope="col"
                            >
                                Developer
                            </th>
                            <th
                                class="h-11 px-4 text-left align-middle text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                scope="col"
                            >
                                Role
                            </th>
                            <th
                                class="h-11 px-4 text-left align-middle text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                scope="col"
                            >
                                Location
                            </th>
                            <th
                                class="h-11 px-4 text-left align-middle text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                scope="col"
                            >
                                Phone
                            </th>
                            <th
                                class="h-11 px-4 text-left align-middle text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                scope="col"
                            >
                                Experience
                            </th>
                            <th
                                class="h-11 px-4 text-left align-middle text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                scope="col"
                            >
                                Salary
                            </th>
                            <th
                                class="h-11 w-16 px-4 text-center align-middle text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                scope="col"
                            >
                                CV
                            </th>
                            <th
                                class="h-11 px-4 text-left align-middle text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                scope="col"
                            >
                                Availability
                            </th>
                            <th
                                class="h-11 px-4 text-left align-middle text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                scope="col"
                            >
                                Skills
                            </th>
                            <th
                                class="h-11 px-4 text-right align-middle text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                scope="col"
                            >
                                Profile
                            </th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:nth-child(odd)]:bg-muted/15">
                        <tr v-for="i in 8" :key="i" class="border-b border-border/40">
                            <td class="px-4 py-3">
                                <div
                                    class="h-4 w-4 animate-pulse rounded bg-muted-foreground/20"
                                />
                            </td>
                            <td class="px-4 py-3">
                                <div
                                    class="h-4 w-40 max-w-full animate-pulse rounded-md bg-muted-foreground/15"
                                />
                            </td>
                            <td class="px-4 py-3">
                                <div
                                    class="h-4 w-28 animate-pulse rounded-md bg-muted-foreground/15"
                                />
                            </td>
                            <td class="px-4 py-3">
                                <div
                                    class="h-4 w-24 animate-pulse rounded-md bg-muted-foreground/15"
                                />
                            </td>
                            <td class="px-4 py-3">
                                <div
                                    class="h-4 w-28 animate-pulse rounded-md bg-muted-foreground/15"
                                />
                            </td>
                            <td class="px-4 py-3">
                                <div
                                    class="h-4 w-10 animate-pulse rounded-md bg-muted-foreground/15"
                                />
                            </td>
                            <td class="px-4 py-3">
                                <div
                                    class="h-4 w-32 animate-pulse rounded-md bg-muted-foreground/15"
                                />
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div
                                    class="mx-auto h-8 w-8 animate-pulse rounded-md bg-muted-foreground/15"
                                />
                            </td>
                            <td class="px-4 py-3">
                                <div
                                    class="h-4 w-36 animate-pulse rounded-md bg-muted-foreground/15"
                                />
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1.5">
                                    <div
                                        class="h-5 w-14 animate-pulse rounded-full bg-muted-foreground/15"
                                    />
                                    <div
                                        class="h-5 w-16 animate-pulse rounded-full bg-muted-foreground/15"
                                    />
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div
                                    class="ml-auto h-8 w-8 animate-pulse rounded-md bg-muted-foreground/15"
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div
            v-else-if="!loading && developers.length === 0"
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
        <template v-else-if="!loading">
            <div
                v-if="viewLayout === 'cards'"
                class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
            >
                <DeveloperCard
                    v-for="developer in developers"
                    :key="developer.id"
                    :developer="developer"
                    :selectable="true"
                    :model-value="
                        compareIds.includes(developerNumericId(developer))
                    "
                    @update:model-value="
                        onCardSelectionChange(developer.id, $event)
                    "
                />
            </div>

            <div
                v-else
                role="region"
                aria-label="Developers in table view"
                class="overflow-hidden rounded-2xl border border-border/80 bg-card shadow-sm ring-1 ring-black/[0.04] dark:ring-white/[0.06]"
            >
                <div class="overflow-x-auto [-webkit-overflow-scrolling:touch]">
                    <Table class="min-w-[86rem] text-sm">
                        <TableHeader
                            class="[&_tr]:border-border/60 [&_tr]:border-b [&_tr]:bg-gradient-to-b [&_tr]:from-muted/55 [&_tr]:to-muted/25"
                        >
                            <TableRow class="border-0 hover:bg-transparent">
                                <TableHead
                                    scope="col"
                                    class="w-12 py-3.5 pl-4 pr-2 text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                >
                                    <span class="sr-only">Select</span>
                                </TableHead>
                                <TableHead
                                    scope="col"
                                    class="min-w-[11rem] py-3.5 pr-4 pl-2 text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                >
                                    Developer
                                </TableHead>
                                <TableHead
                                    scope="col"
                                    class="min-w-[8.5rem] px-4 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                >
                                    Role
                                </TableHead>
                                <TableHead
                                    scope="col"
                                    class="min-w-[7.5rem] px-4 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                >
                                    Location
                                </TableHead>
                                <TableHead
                                    scope="col"
                                    class="min-w-[9rem] px-4 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                >
                                    Phone
                                </TableHead>
                                <TableHead
                                    scope="col"
                                    class="w-24 px-4 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                >
                                    Exp.
                                </TableHead>
                                <TableHead
                                    scope="col"
                                    class="min-w-[10.5rem] px-4 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                >
                                    Salary
                                </TableHead>
                                <TableHead
                                    scope="col"
                                    class="w-16 px-2 py-3.5 text-center text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                >
                                    CV
                                </TableHead>
                                <TableHead
                                    scope="col"
                                    class="min-w-[10rem] px-4 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                >
                                    Availability
                                </TableHead>
                                <TableHead
                                    scope="col"
                                    class="min-w-[14rem] px-4 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                >
                                    Skills
                                </TableHead>
                                <TableHead
                                    scope="col"
                                    class="w-24 py-3.5 pr-4 pl-2 text-right text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                >
                                    Profile
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="developer in developers"
                                :key="developer.id"
                                :data-state="
                                    compareIds.includes(
                                        developerNumericId(developer),
                                    )
                                        ? 'selected'
                                        : undefined
                                "
                                class="group border-border/50 transition-colors odd:bg-muted/[0.12] hover:bg-primary/[0.04]"
                            >
                                <TableCell class="py-3.5 pl-4 pr-2 align-middle">
                                    <Checkbox
                                        :id="`dev-select-${developer.id}`"
                                        :model-value="
                                            compareIds.includes(
                                                developerNumericId(developer),
                                            )
                                        "
                                        :aria-label="`Select ${developer.name}`"
                                        @update:model-value="
                                            onTableCheckboxUpdate(
                                                developer,
                                                $event,
                                            )
                                        "
                                    />
                                </TableCell>
                                <TableCell class="py-3.5 pr-4 pl-2 align-middle">
                                    <div class="flex flex-col gap-0.5">
                                        <span
                                            class="font-medium text-foreground"
                                            >{{ developer.name }}</span
                                        >
                                        <span
                                            class="line-clamp-1 text-xs text-muted-foreground"
                                            >{{ developer.email }}</span
                                        >
                                        <div
                                            v-if="developer.recommended_by_us"
                                            class="mt-1 inline-flex w-fit items-center gap-1 rounded-full border border-amber-500/35 bg-amber-500/10 px-2 py-0.5 text-[11px] font-medium text-amber-800 dark:text-amber-200"
                                        >
                                            <Star
                                                class="size-3 shrink-0 fill-amber-500/80 text-amber-600 dark:text-amber-400"
                                                aria-hidden="true"
                                            />
                                            Recommended
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell
                                    class="px-4 py-3.5 align-middle text-muted-foreground"
                                >
                                    <span class="line-clamp-2 leading-snug">{{
                                        developer.job_title?.name ?? '—'
                                    }}</span>
                                </TableCell>
                                <TableCell
                                    class="max-w-[12rem] px-4 py-3.5 align-middle text-muted-foreground"
                                >
                                    <span class="line-clamp-2 leading-snug">{{
                                        developer.location?.label ?? '—'
                                    }}</span>
                                </TableCell>
                                <TableCell
                                    class="max-w-[11rem] px-4 py-3.5 align-middle tabular-nums text-muted-foreground"
                                >
                                    <a
                                        v-if="developer.phone"
                                        :href="`tel:${developer.phone.replace(/\s+/g, '')}`"
                                        class="line-clamp-2 text-primary underline-offset-2 hover:underline"
                                        :aria-label="`Call ${developer.name} at ${developer.phone}`"
                                    >
                                        {{ developer.phone }}
                                    </a>
                                    <span v-else class="line-clamp-2">—</span>
                                </TableCell>
                                <TableCell
                                    class="px-4 py-3.5 align-middle tabular-nums text-muted-foreground"
                                >
                                    {{ developer.years_of_experience }}
                                    <span class="text-xs text-muted-foreground/80"
                                        >yrs</span
                                    >
                                </TableCell>
                                <TableCell
                                    class="max-w-[14rem] px-4 py-3.5 align-middle text-muted-foreground"
                                >
                                    <span
                                        class="line-clamp-3 text-xs leading-snug tabular-nums"
                                        >{{
                                            developerTableSalaryLabel(
                                                developer,
                                            )
                                        }}</span
                                    >
                                </TableCell>
                                <TableCell
                                    class="px-2 py-3.5 text-center align-middle"
                                >
                                    <Button
                                        v-if="developer.cv_path_url"
                                        variant="ghost"
                                        size="icon"
                                        class="size-9 shrink-0 text-muted-foreground hover:text-foreground"
                                        as-child
                                    >
                                        <a
                                            :href="developer.cv_path_url"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            :aria-label="`Open CV for ${developer.name}`"
                                        >
                                            <FileText
                                                class="size-4"
                                                aria-hidden="true"
                                            />
                                        </a>
                                    </Button>
                                    <span
                                        v-else
                                        class="text-xs text-muted-foreground"
                                        >—</span
                                    >
                                </TableCell>
                                <TableCell class="px-4 py-3.5 align-middle">
                                    <div
                                        class="flex min-w-0 max-w-[13rem] flex-col gap-1"
                                    >
                                        <Badge
                                            :variant="
                                                developer.is_available
                                                    ? 'default'
                                                    : 'secondary'
                                            "
                                            class="w-fit pointer-events-none font-normal"
                                        >
                                            {{
                                                developer.is_available
                                                    ? 'Available'
                                                    : 'Unavailable'
                                            }}
                                        </Badge>
                                        <span
                                            v-if="
                                                availabilityTypeLabels(
                                                    developer,
                                                )
                                            "
                                            class="line-clamp-2 text-[11px] leading-snug text-muted-foreground"
                                        >
                                            {{
                                                availabilityTypeLabels(
                                                    developer,
                                                )
                                            }}
                                        </span>
                                    </div>
                                </TableCell>
                                <TableCell class="max-w-xs px-4 py-3.5 align-middle">
                                    <div
                                        class="flex flex-wrap items-center gap-1"
                                    >
                                        <Badge
                                            v-for="skill in visibleSkillTags(
                                                developer,
                                            ).tags"
                                            :key="skill"
                                            variant="outline"
                                            class="border-border/70 bg-background/80 px-2 py-0 text-[11px] font-normal text-muted-foreground"
                                        >
                                            {{ skill }}
                                        </Badge>
                                        <span
                                            v-if="
                                                visibleSkillTags(developer)
                                                    .more > 0
                                            "
                                            class="text-[11px] text-muted-foreground tabular-nums"
                                        >
                                            +{{
                                                visibleSkillTags(developer).more
                                            }}
                                        </span>
                                    </div>
                                </TableCell>
                                <TableCell
                                    class="py-3.5 pr-4 pl-2 text-right align-middle"
                                >
                                    <Button
                                        v-if="developerProfileHref(developer)"
                                        variant="ghost"
                                        size="icon"
                                        class="size-9 shrink-0 text-muted-foreground hover:text-foreground"
                                        as-child
                                    >
                                        <Link
                                            :href="
                                                developerProfileHref(
                                                    developer,
                                                ) as string
                                            "
                                            :aria-label="`Open profile for ${developer.name}`"
                                        >
                                            <ExternalLink
                                                class="size-4"
                                                aria-hidden="true"
                                            />
                                        </Link>
                                    </Button>
                                    <span
                                        v-else
                                        class="text-xs text-muted-foreground"
                                        >—</span
                                    >
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
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

        <DeveloperOfferForm
            :open="offerFormOpen"
            :store-url="developerOffersStoreUrl ?? ''"
            :selected-developer-ids="compareIds"
            @update:open="offerFormOpen = $event"
            @success="onOfferSuccess"
        />

        <DeveloperCompareDialog
            :open="compareDialogOpen"
            :developers="compareDevelopers"
            @update:open="compareDialogOpen = $event"
        />
    </section>
</template>
