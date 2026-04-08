<script setup lang="ts">
import {
    Check,
    LayoutGrid,
    Scale,
    Search,
    Send,
    Table2,
} from 'lucide-vue-next';
import { defineAsyncComponent } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
const DeveloperFiltersPanelContent = defineAsyncComponent(
    () => import('@/components/DeveloperFiltersPanelContent.vue'),
);

defineProps<{
    searchQuery: string;
    paginationTotal: number | null;
    viewLayout: 'cards' | 'table';
    compareIdsLength: number;
    canSelectDevelopers: boolean;
    allCurrentSelected: boolean;
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
    showSuperAdminFilters: boolean;
    filterNullField: string[];
    nullFieldSelectOpen: boolean;
    aiPromptText: string;
    aiPromptCopied: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:searchQuery', v: string): void;
    (e: 'update:viewLayout', v: 'cards' | 'table'): void;
    (e: 'clearCompare'): void;
    (e: 'openCompareDialog'): void;
    (e: 'toggleSelectAll'): void;
    (e: 'openOfferForm'): void;
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
    (e: 'update:filterNullField', v: string[]): void;
    (e: 'update:nullFieldSelectOpen', v: boolean): void;
    (e: 'clearFilters'): void;
    (e: 'copyAiPrompt'): void;
}>();
</script>

<template>
    <aside
        data-tour="developer-filters"
        class="w-full shrink-0 rounded-xl border border-border bg-card/95 shadow-sm backdrop-blur-md supports-[backdrop-filter]:bg-card/80 lg:sticky lg:top-24 lg:max-h-[calc(100vh-7rem)] lg:w-[min(100%,22rem)] lg:overflow-y-auto lg:overscroll-y-contain xl:w-96"
        aria-label="Developer search and filters"
    >
        <div class="px-3 py-4 sm:px-4 sm:py-5">
            <div class="mb-4 space-y-3 border-b border-border/70 pb-4">
                <div
                    data-tour="developer-search"
                    class="relative flex min-w-0 items-center rounded-lg border border-input bg-muted/30 transition-colors focus-within:border-primary focus-within:bg-background focus-within:ring-2 focus-within:ring-primary/20"
                >
                    <Search
                        class="pointer-events-none absolute left-3.5 h-4 w-4 shrink-0 text-muted-foreground"
                        aria-hidden="true"
                    />
                    <Input
                        :model-value="searchQuery"
                        type="search"
                        placeholder="Search by name, email, or skills..."
                        class="h-10 w-full min-w-0 border-0 bg-transparent pr-4 pl-10 shadow-none focus-visible:ring-0"
                        autocomplete="off"
                        @update:model-value="
                            emit('update:searchQuery', String($event ?? ''))
                        "
                    />
                </div>

                <div class="flex flex-col gap-3">
                    <p
                        v-if="paginationTotal !== null"
                        class="text-sm font-medium text-muted-foreground tabular-nums"
                        aria-live="polite"
                    >
                        <span class="text-foreground">{{
                            paginationTotal
                        }}</span>
                        {{ paginationTotal === 1 ? 'developer' : 'developers' }}
                    </p>

                    <div
                        data-tour="developer-view-toggle"
                        class="inline-flex w-full shrink-0 rounded-lg border border-border/80 bg-muted/40 p-0.5 shadow-inner"
                        role="group"
                        aria-label="Developer list layout"
                    >
                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            :class="[
                                'h-8 min-w-0 flex-1 gap-1.5 rounded-md px-2',
                                viewLayout === 'cards'
                                    ? 'bg-background text-foreground shadow-sm hover:bg-background'
                                    : 'text-muted-foreground hover:bg-transparent hover:text-foreground',
                            ]"
                            :aria-pressed="viewLayout === 'cards'"
                            @click="emit('update:viewLayout', 'cards')"
                        >
                            <LayoutGrid
                                class="size-4 shrink-0"
                                aria-hidden="true"
                            />
                            <span>Cards</span>
                        </Button>
                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            :class="[
                                'h-8 min-w-0 flex-1 gap-1.5 rounded-md px-2',
                                viewLayout === 'table'
                                    ? 'bg-background text-foreground shadow-sm hover:bg-background'
                                    : 'text-muted-foreground hover:bg-transparent hover:text-foreground',
                            ]"
                            :aria-pressed="viewLayout === 'table'"
                            @click="emit('update:viewLayout', 'table')"
                        >
                            <Table2
                                class="size-4 shrink-0"
                                aria-hidden="true"
                            />
                            <span>Table</span>
                        </Button>
                    </div>

                    <div
                        data-tour="developer-compare"
                        class="flex flex-wrap items-center gap-1.5"
                    >
                        <Button
                            v-if="compareIdsLength > 0"
                            variant="ghost"
                            size="sm"
                            class="h-8 gap-1.5 px-2.5 text-muted-foreground hover:bg-muted/60 hover:text-foreground"
                            @click="emit('clearCompare')"
                        >
                            <span class="tabular-nums"
                                >{{ compareIdsLength }}/2</span
                            >
                            Clear
                        </Button>
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <span class="inline-flex">
                                        <Button
                                            :variant="
                                                compareIdsLength === 2
                                                    ? 'default'
                                                    : 'outline'
                                            "
                                            size="sm"
                                            class="h-8 shrink-0 gap-1.5"
                                            :disabled="compareIdsLength !== 2"
                                            @click="emit('openCompareDialog')"
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
                                        compareIdsLength > 2
                                            ? 'Compare only works for 2 selections'
                                            : compareIdsLength === 2
                                              ? 'Compare selected developers'
                                              : 'Select 2 developers to compare'
                                    }}
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </div>

                    <template v-if="canSelectDevelopers">
                        <div class="flex flex-col gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                class="h-8 w-full shrink-0 gap-1.5"
                                @click="emit('toggleSelectAll')"
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
                                    compareIdsLength > 0 ? 'default' : 'outline'
                                "
                                size="sm"
                                class="h-8 w-full shrink-0 gap-1.5"
                                :disabled="compareIdsLength === 0"
                                @click="emit('openOfferForm')"
                            >
                                <Send class="size-4" aria-hidden="true" />
                                Send offer
                                <span v-if="compareIdsLength > 0">
                                    ({{ compareIdsLength }})
                                </span>
                            </Button>
                        </div>
                    </template>
                </div>
            </div>

            <Suspense>
                <DeveloperFiltersPanelContent
                    variant="sidebar"
                    :filter-job-title="filterJobTitle"
                    :filter-skill="filterSkill"
                    :filter-badge="filterBadge"
                    :filter-availability-type="filterAvailabilityType"
                    :filter-has-urls="filterHasUrls"
                    :is-available="isAvailable"
                    :is-recommended="isRecommended"
                    :years-min="yearsMin"
                    :years-max="yearsMax"
                    :job-title-select-open="jobTitleSelectOpen"
                    :skill-select-open="skillSelectOpen"
                    :badge-select-open="badgeSelectOpen"
                    :availability-type-select-open="availabilityTypeSelectOpen"
                    :has-urls-select-open="hasUrlsSelectOpen"
                    :show-super-admin-filters="showSuperAdminFilters"
                    :filter-null-field="filterNullField"
                    :null-field-select-open="nullFieldSelectOpen"
                    :pagination-total="paginationTotal"
                    :ai-prompt-text="aiPromptText"
                    :ai-prompt-copied="aiPromptCopied"
                    @update:filter-job-title="
                        emit('update:filterJobTitle', $event)
                    "
                    @update:filter-skill="emit('update:filterSkill', $event)"
                    @update:filter-badge="emit('update:filterBadge', $event)"
                    @update:filter-availability-type="
                        emit('update:filterAvailabilityType', $event)
                    "
                    @update:filter-has-urls="
                        emit('update:filterHasUrls', $event)
                    "
                    @update:is-available="emit('update:isAvailable', $event)"
                    @update:is-recommended="
                        emit('update:isRecommended', $event)
                    "
                    @update:years-min="emit('update:yearsMin', $event)"
                    @update:years-max="emit('update:yearsMax', $event)"
                    @update:job-title-select-open="
                        emit('update:jobTitleSelectOpen', $event)
                    "
                    @update:skill-select-open="
                        emit('update:skillSelectOpen', $event)
                    "
                    @update:badge-select-open="
                        emit('update:badgeSelectOpen', $event)
                    "
                    @update:availability-type-select-open="
                        emit('update:availabilityTypeSelectOpen', $event)
                    "
                    @update:has-urls-select-open="
                        emit('update:hasUrlsSelectOpen', $event)
                    "
                    @update:filter-null-field="
                        emit('update:filterNullField', $event)
                    "
                    @update:null-field-select-open="
                        emit('update:nullFieldSelectOpen', $event)
                    "
                    @clear-filters="emit('clearFilters')"
                    @copy-ai-prompt="emit('copyAiPrompt')"
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
    </aside>
</template>
