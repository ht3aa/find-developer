<script setup lang="ts">
import {
    Check,
    ChevronDown,
    Copy,
    Plus,
    Sparkles,
    Trash2,
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
    createRoleBandRow,
    type ExperienceLevel,
    type UiRoleBandRow,
} from '@/lib/roleBandFilters';
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
    roleBandRows: UiRoleBandRow[];
    /** Controlled open state for role row job title selects (one row at a time). */
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
    /** Super admin: filter rows where selected columns are null/empty. */
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

function patchRow(
    clientId: string,
    patch: Partial<Pick<UiRoleBandRow, 'jobTitle' | 'level'>>,
): void {
    const next = props.roleBandRows.map((r) =>
        r.clientId === clientId ? { ...r, ...patch } : r,
    );
    emit('update:roleBandRows', next);
}

function onRowJobTitle(clientId: string, v: string | string[] | null): void {
    const raw = Array.isArray(v) ? v[0] : v;
    patchRow(clientId, { jobTitle: raw ? String(raw) : '' });
}

function setRowLevel(clientId: string, level: ExperienceLevel): void {
    patchRow(clientId, { level });
}

function removeRow(clientId: string): void {
    const next = props.roleBandRows.filter((r) => r.clientId !== clientId);
    emit('update:roleBandRows', next.length > 0 ? next : [createRoleBandRow()]);
}

function addRoleBandRow(): void {
    emit('update:roleBandRows', [...props.roleBandRows, createRoleBandRow()]);
}

const levelChoices: { key: Exclude<ExperienceLevel, ''>; label: string }[] = [
    { key: 'junior', label: 'Junior' },
    { key: 'mid', label: 'Mid' },
    { key: 'senior', label: 'Senior' },
];

const roleFiltersOpen = ref(false);
</script>

<template>
    <div class="mx-auto w-full max-w-4xl pr-8 pb-5 sm:pr-10 sm:pb-6">
        <div
            class="sticky top-0 z-10 mb-6 flex flex-col gap-4 rounded-2xl border border-border/80 bg-gradient-to-br from-primary/[0.07] via-card/95 to-card/95 p-4 shadow-md ring-1 shadow-black/5 ring-black/[0.04] backdrop-blur-md supports-[backdrop-filter]:via-card/90 supports-[backdrop-filter]:to-card/90 sm:flex-row sm:items-center sm:justify-between sm:gap-6 sm:p-5 dark:ring-white/[0.06]"
        >
            <div class="min-w-0 space-y-1">
                <SheetTitle
                    class="text-xl font-semibold tracking-tight text-foreground"
                >
                    Advanced filters
                </SheetTitle>
                <p class="text-sm leading-snug text-muted-foreground">
                    Add role rows (job title + band) combined with OR, or use
                    custom fields below.
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
                        {{
                            props.paginationTotal === 1
                                ? 'developer'
                                : 'developers'
                        }}
                    </span>
                </div>
            </div>
        </div>
        <SheetDescription class="sr-only">
            Filter developers by role bands, job title, skills, badges,
            availability type, has URLs, availability status, recommended
            status, years of experience, and for super admins missing profile
            fields.
        </SheetDescription>

        <Collapsible
            v-model:open="roleFiltersOpen"
            class="mb-8 overflow-hidden rounded-2xl border border-border/80 bg-card shadow-sm ring-1 ring-black/[0.03] dark:ring-white/[0.06]"
        >
            <CollapsibleTrigger
                class="flex w-full items-start justify-between gap-3 bg-muted/20 px-4 py-4 text-left transition-colors hover:bg-muted/30 sm:px-5 sm:py-4 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none focus-visible:ring-offset-background"
            >
                <div class="min-w-0 flex-1">
                    <p
                        class="text-[11px] font-bold tracking-[0.12em] text-primary uppercase sm:text-xs"
                    >
                        Role filters
                    </p>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Each row is one job title and experience band. Multiple
                        rows match
                        <span class="font-medium text-foreground">any</span> row
                        (OR). Rows with a title and level replace the custom job
                        title and min/max years until you change those fields.
                    </p>
                    <div
                        class="mt-3 flex flex-wrap gap-2 text-[11px] text-muted-foreground"
                        aria-hidden="true"
                    >
                        <span
                            class="inline-flex rounded-full bg-background px-2.5 py-1 font-medium ring-1 ring-border/80"
                            >Junior ≤2 yrs</span
                        >
                        <span
                            class="inline-flex rounded-full bg-background px-2.5 py-1 font-medium ring-1 ring-border/80"
                            >Mid 3–5 yrs</span
                        >
                        <span
                            class="inline-flex rounded-full bg-background px-2.5 py-1 font-medium ring-1 ring-border/80"
                            >Senior 6+ yrs</span
                        >
                    </div>
                </div>
                <span
                    class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-full border border-border/80 bg-muted/50 text-muted-foreground transition-transform duration-200"
                    :class="{ 'rotate-180': roleFiltersOpen }"
                >
                    <ChevronDown class="size-4" aria-hidden="true" />
                </span>
            </CollapsibleTrigger>
            <CollapsibleContent
                class="overflow-hidden border-t border-border/70 bg-muted/15"
            >
                <div class="space-y-4 px-4 py-5 sm:px-6 sm:py-6">
                <div
                    v-for="row in props.roleBandRows"
                    :key="row.clientId"
                    class="rounded-xl border border-border/80 bg-muted/15 p-4 shadow-sm sm:p-5"
                >
                    <div class="mb-3 flex items-start justify-between gap-3">
                        <Label
                            :for="`role-band-title-${row.clientId}`"
                            class="text-xs font-semibold tracking-wide text-foreground/90 uppercase"
                        >
                            Job title
                        </Label>
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            class="size-8 shrink-0 text-muted-foreground hover:text-destructive"
                            :aria-label="'Remove role filter row'"
                            @click="removeRow(row.clientId)"
                        >
                            <Trash2 class="size-4" aria-hidden="true" />
                        </Button>
                    </div>
                    <SearchableSelect
                        :id="`role-band-title-${row.clientId}`"
                        :model-value="row.jobTitle || null"
                        :open="
                            props.roleBandJobTitleOpenClientId === row.clientId
                        "
                        options-url="/api/job-titles"
                        placeholder="Search job titles…"
                        :max-options="50"
                        @update:model-value="
                            onRowJobTitle(row.clientId, $event)
                        "
                        @update:open="
                            emit('roleBandJobTitleOpen', {
                                clientId: row.clientId,
                                open: $event,
                            })
                        "
                    />
                    <p
                        class="mt-4 mb-2 text-xs font-semibold tracking-wide text-foreground/90 uppercase"
                    >
                        Experience
                    </p>
                    <div
                        class="grid grid-cols-3 gap-2 sm:gap-3"
                        role="group"
                        :aria-label="'Experience level for this row'"
                    >
                        <button
                            v-for="choice in levelChoices"
                            :key="choice.key"
                            type="button"
                            :aria-pressed="row.level === choice.key"
                            class="flex min-h-11 min-w-0 flex-col items-center justify-center rounded-xl border px-2 py-2.5 text-center text-sm font-semibold tracking-tight transition-all duration-150 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none active:scale-[0.98] sm:min-h-12 sm:px-3 sm:py-3"
                            :class="
                                row.level === choice.key
                                    ? 'border-primary bg-primary text-primary-foreground shadow-md shadow-primary/25'
                                    : 'border-border/90 bg-card text-foreground hover:border-primary/40 hover:bg-muted/60 hover:shadow-sm'
                            "
                            @click="setRowLevel(row.clientId, choice.key)"
                        >
                            {{ choice.label }}
                        </button>
                    </div>
                </div>
                <Button
                    type="button"
                    variant="outline"
                    class="w-full gap-2 border-dashed sm:w-auto"
                    @click="addRoleBandRow"
                >
                    <Plus class="size-4" aria-hidden="true" />
                    Add role filter
                </Button>
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
            <div
                v-if="props.showSuperAdminFilters"
                class="space-y-2 rounded-xl border border-amber-500/35 bg-amber-500/5 p-3 sm:col-span-2"
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
                    @update:open="
                        emit('update:nullFieldSelectOpen', $event)
                    "
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
