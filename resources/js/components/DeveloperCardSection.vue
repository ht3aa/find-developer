<script setup lang="ts">
import { refDebounced, useClipboard } from '@vueuse/core';
import {
    Award,
    Check,
    Copy,
    FilterX,
    Scale,
    Search,
    Send,
    SlidersHorizontal,
    Sparkles,
    Users,
} from 'lucide-vue-next';
import { computed, defineAsyncComponent, onMounted, ref, watch } from 'vue';
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
import { Input } from '@/components/ui/input';
import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';
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
import {
    apiBandToRow,
    createRoleBandRow,
    rowsToApiBands,
    type UiRoleBandRow,
} from '@/lib/roleBandFilters';
import type { Developer } from '@/types/developer';
import {
    availabilityTypeOptions,
    hasUrlsOptions,
} from '@/utils/developerEnums';

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

function onCardSelectionChange(id: number, selected: boolean): void {
    if (selected) {
        if (!compareIds.value.includes(id)) {
            const dev = developers.value.find((d) => d.id === id);
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
            (d) => d.id !== id,
        );
    }
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
    const ids = developers.value.map((d) => Number(d.id));
    compareIds.value = ids;
    compareDevelopersData.value = developers.value.filter((d) =>
        ids.includes(d.id),
    );
}

const allCurrentSelected = computed(
    () =>
        developers.value.length > 0 &&
        developers.value.every((d) => compareIds.value.includes(d.id)),
);

function onOfferSuccess(): void {
    offerFormOpen.value = false;
    clearSelection();
}

const compareSelectionCount = computed(() => compareIds.value.length);

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

const API_BASE = '/api/developers';
const initialFilters = parseFiltersFromUrl();
const searchQuery = ref(initialFilters.search ?? '');
const debouncedQuery = refDebounced(searchQuery, 500);
const roleBandRows = ref<UiRoleBandRow[]>(
    initialFilters.initialRoleBands.length > 0
        ? initialFilters.initialRoleBands.map((b) => apiBandToRow(b))
        : [createRoleBandRow()],
);
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
/** Which role-band row’s job title combobox is open (controlled; matches sheet + dialog focus). */
const roleBandJobTitleOpenClientId = ref<string | null>(null);

function onJobTitleOpenChange(open: boolean): void {
    jobTitleSelectOpen.value = open;
    if (open) {
        roleBandJobTitleOpenClientId.value = null;
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
        roleBandJobTitleOpenClientId.value = null;
        badgeSelectOpen.value = false;
        availabilityTypeSelectOpen.value = false;
        hasUrlsSelectOpen.value = false;
    }
}

function onBadgeOpenChange(open: boolean): void {
    badgeSelectOpen.value = open;
    if (open) {
        jobTitleSelectOpen.value = false;
        roleBandJobTitleOpenClientId.value = null;
        skillSelectOpen.value = false;
        availabilityTypeSelectOpen.value = false;
        hasUrlsSelectOpen.value = false;
    }
}

function onAvailabilityTypeOpenChange(open: boolean): void {
    availabilityTypeSelectOpen.value = open;
    if (open) {
        jobTitleSelectOpen.value = false;
        roleBandJobTitleOpenClientId.value = null;
        skillSelectOpen.value = false;
        badgeSelectOpen.value = false;
        hasUrlsSelectOpen.value = false;
    }
}

function onHasUrlsOpenChange(open: boolean): void {
    hasUrlsSelectOpen.value = open;
    if (open) {
        jobTitleSelectOpen.value = false;
        roleBandJobTitleOpenClientId.value = null;
        skillSelectOpen.value = false;
        badgeSelectOpen.value = false;
        availabilityTypeSelectOpen.value = false;
    }
}

function onRoleBandJobTitleOpen(payload: {
    clientId: string;
    open: boolean;
}): void {
    if (payload.open) {
        roleBandJobTitleOpenClientId.value = payload.clientId;
        jobTitleSelectOpen.value = false;
        skillSelectOpen.value = false;
        badgeSelectOpen.value = false;
        availabilityTypeSelectOpen.value = false;
        hasUrlsSelectOpen.value = false;
        return;
    }
    if (roleBandJobTitleOpenClientId.value === payload.clientId) {
        roleBandJobTitleOpenClientId.value = null;
    }
}

function getFilters(): DeveloperFilters {
    const shared: Pick<
        DeveloperFilters,
        | 'search'
        | 'skill'
        | 'badge'
        | 'availabilityType'
        | 'hasUrls'
        | 'isAvailable'
        | 'isRecommended'
        | 'ids'
    > = {
        search: debouncedQuery.value,
        skill: filterSkill.value,
        badge: filterBadge.value,
        availabilityType: filterAvailabilityType.value,
        hasUrls: filterHasUrls.value,
        isAvailable: isAvailable.value,
        isRecommended: isRecommended.value,
        ids: props.developerIds?.length ? props.developerIds : undefined,
    };
    const roleBands = rowsToApiBands(roleBandRows.value);
    if (roleBands.length > 0) {
        return {
            ...shared,
            roleBands,
            jobTitle: [],
            yearsMin: '',
            yearsMax: '',
        };
    }
    return {
        ...shared,
        jobTitle: filterJobTitle.value,
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
    roleBandRows.value = [createRoleBandRow()];
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

function onFilterJobTitleUpdate(v: string[]): void {
    roleBandRows.value = [createRoleBandRow()];
    filterJobTitle.value = v;
}

function onYearsMinUpdate(v: string): void {
    roleBandRows.value = [createRoleBandRow()];
    yearsMin.value = v;
}

function onYearsMaxUpdate(v: string): void {
    roleBandRows.value = [createRoleBandRow()];
    yearsMax.value = v;
}

function onRoleBandRowsUpdate(rows: UiRoleBandRow[]): void {
    roleBandRows.value = rows;
    if (rowsToApiBands(rows).length > 0) {
        filterJobTitle.value = [];
        yearsMin.value = '';
        yearsMax.value = '';
    }
    fetchDevelopers(buildDevelopersApiUrl(API_BASE, getFilters()));
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
    const bandCount = rowsToApiBands(roleBandRows.value).length;
    if (bandCount > 0) {
        count += bandCount;
    } else {
        if (filterJobTitle.value.length > 0)
            count += filterJobTitle.value.length;
        if (yearsMin.value) count++;
        if (yearsMax.value) count++;
    }
    if (filterSkill.value.length > 0) count += filterSkill.value.length;
    if (filterBadge.value.length > 0) count += filterBadge.value.length;
    if (filterAvailabilityType.value.length > 0)
        count += filterAvailabilityType.value.length;
    if (filterHasUrls.value.length > 0) count += filterHasUrls.value.length;
    if (isAvailable.value && isAvailable.value !== 'all') count++;
    if (isRecommended.value && isRecommended.value !== 'all') count++;
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
        roleBandJobTitleOpenClientId.value = null;
    }
});

onMounted(() => {
    fetchDevelopers();
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
                                variant="default"
                                size="sm"
                                class="relative h-8 shrink-0 gap-1.5 shadow-sm"
                                :class="{
                                    'animate-filters-attention': !advancedOpen,
                                }"
                            >
                                <SlidersHorizontal
                                    class="size-4"
                                    aria-hidden="true"
                                />
                                <span class="hidden sm:inline">Filters</span>
                                <Badge
                                    v-if="activeFilterCount > 0"
                                    variant="secondary"
                                    class="absolute -top-1 -right-1 flex size-5 min-w-5 items-center justify-center rounded-full border border-primary-foreground/25 bg-primary-foreground px-1 text-[10px] font-semibold text-primary"
                                >
                                    {{ activeFilterCount }}
                                </Badge>
                            </Button>
                        </SheetTrigger>
                        <SheetContent
                            side="top"
                            class="flex max-h-[85vh] flex-col gap-0 overflow-hidden border-b p-0"
                        >
                            <div
                                class="min-h-0 flex-1 overflow-y-auto overscroll-y-contain px-4 pt-8 pr-12 pb-6 sm:px-6 sm:pt-8 sm:pr-14 sm:pb-8"
                            >
                                <Suspense>
                                    <DeveloperFiltersPanelContent
                                        v-if="advancedOpen"
                                        :role-band-rows="roleBandRows"
                                        :role-band-job-title-open-client-id="
                                            roleBandJobTitleOpenClientId
                                        "
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
                                        :job-title-select-open="
                                            jobTitleSelectOpen
                                        "
                                        :skill-select-open="skillSelectOpen"
                                        :badge-select-open="badgeSelectOpen"
                                        :availability-type-select-open="
                                            availabilityTypeSelectOpen
                                        "
                                        :has-urls-select-open="
                                            hasUrlsSelectOpen
                                        "
                                        :pagination-total="paginationTotal"
                                        :ai-prompt-text="aiPromptText"
                                        :ai-prompt-copied="aiPromptCopied"
                                        @update:filter-job-title="
                                            onFilterJobTitleUpdate($event)
                                        "
                                        @update:filter-skill="
                                            filterSkill = $event
                                        "
                                        @update:filter-badge="
                                            filterBadge = $event
                                        "
                                        @update:filter-availability-type="
                                            filterAvailabilityType = $event
                                        "
                                        @update:filter-has-urls="
                                            filterHasUrls = $event
                                        "
                                        @update:is-available="
                                            isAvailable = $event
                                        "
                                        @update:is-recommended="
                                            isRecommended = $event
                                        "
                                        @update:years-min="
                                            onYearsMinUpdate($event)
                                        "
                                        @update:years-max="
                                            onYearsMaxUpdate($event)
                                        "
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
                                        @update:role-band-rows="
                                            onRoleBandRowsUpdate($event)
                                        "
                                        @role-band-job-title-open="
                                            onRoleBandJobTitleOpen($event)
                                        "
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
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>
            </div>
        </div>

        <div v-if="loading" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
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
                    :selectable="true"
                    :model-value="compareIds.includes(developer.id)"
                    @update:model-value="
                        onCardSelectionChange(developer.id, $event)
                    "
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
