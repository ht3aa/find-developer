<script setup lang="ts">
import { defineAsyncComponent } from 'vue';
import type { UiRoleBandRow } from '@/lib/roleBandFilters';

const DeveloperFiltersPanelContent = defineAsyncComponent(
    () => import('@/components/DeveloperFiltersPanelContent.vue'),
);

defineProps<{
    roleBandRows: UiRoleBandRow[];
    roleBandJobTitleOpenClientId: string | null;
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
    paginationTotal: number | null;
    aiPromptText: string;
    aiPromptCopied: boolean;
}>();

const emit = defineEmits<{
    (
        e: 'roleBandJobTitleOpen',
        payload: { clientId: string; open: boolean },
    ): void;
    (e: 'update:roleBandRows', v: UiRoleBandRow[]): void;
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
    (e: 'applyFilters'): void;
    (e: 'clearFilters'): void;
    (e: 'copyAiPrompt'): void;
}>();
</script>

<template>
    <aside
        data-tour="developer-filters"
        class="w-full shrink-0 rounded-xl border border-border bg-card/95 shadow-sm backdrop-blur-md supports-[backdrop-filter]:bg-card/80 lg:sticky lg:top-24 lg:max-h-[calc(100vh-7rem)] lg:w-[min(100%,22rem)] lg:overflow-y-auto lg:overscroll-y-contain xl:w-96"
        aria-label="Developer filters"
    >
        <div class="px-3 py-4 sm:px-4 sm:py-5">
            <Suspense>
                <DeveloperFiltersPanelContent
                    v-bind="$props"
                    variant="sidebar"
                    @role-band-job-title-open="
                        emit('roleBandJobTitleOpen', $event)
                    "
                    @update:role-band-rows="emit('update:roleBandRows', $event)"
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
                    @apply-filters="emit('applyFilters')"
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
