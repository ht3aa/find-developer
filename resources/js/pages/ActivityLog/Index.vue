<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { refDebounced } from '@vueuse/core';
import {
    Box,
    ClipboardList,
    Copy,
    ExternalLink,
    Loader2,
    Search,
    UserMinus,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import {
    index as activityLogIndex,
    show as activityLogShow,
} from '@/routes/dashboard/activity-log';
import { Checkbox } from '@/components/ui/checkbox';
import type { BreadcrumbItem } from '@/types';
import type { ActivityLogEntry } from '@/types/activity-log';

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatedActivities = {
    data: ActivityLogEntry[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
};

const SUSPENDABLE_CAUSER_TYPES = [
    'App\\Models\\User',
    'App\\Models\\Developer',
];

type Props = {
    activities: PaginatedActivities;
    filters?: {
        search?: string;
        log_name?: string;
        group_by?: string;
    };
    suspendCauserUrl?: string;
};

const props = withDefaults(defineProps<Props>(), {
    filters: () => ({ search: '', log_name: '', group_by: '' }),
    suspendCauserUrl: '',
});

const page = usePage();
const searchQuery = ref(props.filters.search ?? '');
const groupByCauser = ref(props.filters.group_by === 'causer');
const debouncedSearch = refDebounced(searchQuery, 300);
const copiedEmail = ref<string | null>(null);
const suspendingCauserId = ref<string | null>(null);
let copiedEmailTimeout: ReturnType<typeof setTimeout> | null = null;

const modalOpen = ref(false);
const modalProperties = ref<Record<string, unknown> | null>(null);
const modalLoading = ref(false);
const modalActivityId = ref<number | null>(null);
let fetchAbortController: AbortController | null = null;

function propertiesUrl(id: number): string {
    return `${activityLogShow(id).url}/properties`;
}

async function openPropertiesModal(activity: ActivityLogEntry): Promise<void> {
    if (fetchAbortController) {
        fetchAbortController.abort();
        fetchAbortController = null;
    }
    modalActivityId.value = activity.id;
    modalProperties.value = null;
    modalLoading.value = true;
    modalOpen.value = true;
    fetchAbortController = new AbortController();
    try {
        const res = await fetch(propertiesUrl(activity.id), {
            signal: fetchAbortController.signal,
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        if (!res.ok) return;
        const data = await res.json();
        if (modalActivityId.value === activity.id) {
            modalProperties.value = data.properties ?? {};
        }
    } catch {
        if (modalActivityId.value === activity.id) {
            modalProperties.value = {};
        }
    } finally {
        if (modalActivityId.value === activity.id) {
            modalLoading.value = false;
        }
        fetchAbortController = null;
    }
}

type PropertyRow = { property: string; oldValue: unknown; newValue: unknown };

function formatPropertyValue(value: unknown): string {
    if (value === null || value === undefined) return '—';
    if (typeof value === 'object') return JSON.stringify(value);
    return String(value);
}

const modalPropertiesRows = computed<PropertyRow[]>(() => {
    const p = modalProperties.value;
    if (!p || typeof p !== 'object') return [];
    const attrs = (p.attributes as Record<string, unknown>) ?? {};
    const old = (p.old as Record<string, unknown>) ?? {};
    const keys = new Set([...Object.keys(attrs), ...Object.keys(old)]);
    if (keys.size > 0) {
        return [...keys].sort().map((key) => ({
            property: key,
            oldValue: old[key],
            newValue: attrs[key],
        }));
    }
    return Object.entries(p)
        .filter(([k]) => k !== 'attributes' && k !== 'old')
        .sort(([a], [b]) => a.localeCompare(b))
        .map(([key, value]) => ({
            property: key,
            oldValue: undefined,
            newValue: value,
        }));
});

const hasModalProperties = computed(() => modalPropertiesRows.value.length > 0);

async function copyCauserEmail(email: string): Promise<void> {
    try {
        await navigator.clipboard.writeText(email);
        copiedEmail.value = email;
        if (copiedEmailTimeout) clearTimeout(copiedEmailTimeout);
        copiedEmailTimeout = setTimeout(() => {
            copiedEmail.value = null;
        }, 1500);
    } catch {
        // ignore
    }
}

function buildFilters(): Record<string, string | undefined> {
    return {
        search: searchQuery.value || undefined,
        log_name: props.filters.log_name || undefined,
        group_by: groupByCauser.value ? 'causer' : undefined,
    };
}

watch(debouncedSearch, () => {
    router.get(activityLogIndex().url, buildFilters(), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});

watch(groupByCauser, () => {
    router.get(activityLogIndex().url, buildFilters(), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});

const groupedActivities = computed(() => {
    if (props.filters.group_by !== 'causer') {
        return null;
    }
    const groups = new Map<string, ActivityLogEntry[]>();
    for (const a of props.activities.data) {
        const key =
            a.causer_email ??
            `no-email-${a.causer_type ?? 'null'}-${a.causer_id ?? 'null'}`;
        const list = groups.get(key) ?? [];
        list.push(a);
        groups.set(key, list);
    }
    return Array.from(groups.entries()).map(([email, activities]) => ({
        causerEmail: email.startsWith('no-email-') ? null : email,
        activities,
    }));
});

function canSuspendCauser(activity: ActivityLogEntry): boolean {
    if (!activity.causer_type_full || activity.causer_id === null) return false;
    if (activity.causer_already_suspended) return false;
    return SUSPENDABLE_CAUSER_TYPES.includes(activity.causer_type_full);
}

function suspendCauser(activity: ActivityLogEntry): void {
    if (!props.suspendCauserUrl || !canSuspendCauser(activity)) return;
    const email = activity.causer_email ?? 'this user';
    if (
        !window.confirm(
            `Are you sure you want to suspend the developer profile for ${email}?`,
        )
    ) {
        return;
    }
    const key = `${activity.causer_type_full}-${activity.causer_id}`;
    suspendingCauserId.value = key;
    router.post(
        props.suspendCauserUrl,
        {
            causer_type: activity.causer_type_full,
            causer_id: activity.causer_id,
        },
        {
            preserveScroll: true,
            onSuccess: () => router.reload(),
            onFinish: () => {
                suspendingCauserId.value = null;
            },
        },
    );
}

function formatDate(iso: string): string {
    const d = new Date(iso);
    return d.toLocaleString(undefined, {
        dateStyle: 'short',
        timeStyle: 'short',
    });
}

function eventBadgeVariant(
    event: string | null,
): 'default' | 'secondary' | 'destructive' | 'outline' {
    if (!event) return 'secondary';
    if (event === 'created') return 'default';
    if (event === 'updated') return 'outline';
    if (event === 'deleted') return 'destructive';
    return 'secondary';
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Activity Log', href: activityLogIndex().url },
];
</script>

<template>
    <Head title="Activity Log" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Activity Log
                    </h1>
                    <p class="text-muted-foreground">
                        Audit trail of model changes (super admin only)
                    </p>
                </div>
            </div>

            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative flex-1 sm:max-w-sm">
                        <Search
                            class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                        />
                        <Input
                            v-model="searchQuery"
                            type="search"
                            placeholder="Search description, subject, event, causer email..."
                            class="pl-9"
                        />
                    </div>
                    <div
                        class="flex items-center gap-2 rounded-md border px-3 py-2"
                    >
                        <Checkbox
                            id="group-by-causer"
                            :checked="groupByCauser"
                            @update:checked="groupByCauser = $event ?? false"
                        />
                        <label
                            for="group-by-causer"
                            class="cursor-pointer text-sm font-medium"
                        >
                            Group by causer
                        </label>
                    </div>
                </div>
                <p
                    v-if="activities.total > 0"
                    class="text-sm text-muted-foreground"
                >
                    Showing {{ activities.from }}–{{ activities.to }} of
                    {{ activities.total }}
                </p>
            </div>

            <div v-if="activities.data.length > 0" class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead
                                v-if="filters.group_by === 'causer'"
                                class="w-[180px]"
                            >
                                Causer
                            </TableHead>
                            <TableHead class="w-[140px]">Date</TableHead>
                            <TableHead class="w-[90px]">Event</TableHead>
                            <TableHead class="w-[120px]">Subject</TableHead>
                            <TableHead
                                v-if="filters.group_by !== 'causer'"
                                class="w-[180px]"
                            >
                                Causer email
                            </TableHead>
                            <TableHead class="w-[80px]">Log</TableHead>
                            <TableHead class="w-[200px]" />
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <template v-if="groupedActivities">
                            <template
                                v-for="group in groupedActivities"
                                :key="
                                    group.causerEmail ??
                                    `group-${group.activities[0]?.id}`
                                "
                            >
                                <TableRow class="bg-muted/50 font-medium">
                                    <TableCell colspan="6">
                                        {{
                                            group.causerEmail ??
                                            '(No causer email)'
                                        }}
                                    </TableCell>
                                </TableRow>
                                <TableRow
                                    v-for="activity in group.activities"
                                    :key="activity.id"
                                >
                                    <TableCell class="w-[180px]" />
                                    <TableCell
                                        class="text-sm whitespace-nowrap text-muted-foreground"
                                    >
                                        {{ formatDate(activity.created_at) }}
                                    </TableCell>
                                    <TableCell>
                                        <span
                                            :class="[
                                                'inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium',
                                                eventBadgeVariant(
                                                    activity.event,
                                                ) === 'default' &&
                                                    'bg-primary text-primary-foreground',
                                                eventBadgeVariant(
                                                    activity.event,
                                                ) === 'outline' &&
                                                    'border border-input bg-background',
                                                eventBadgeVariant(
                                                    activity.event,
                                                ) === 'destructive' &&
                                                    'bg-destructive/10 text-destructive',
                                                eventBadgeVariant(
                                                    activity.event,
                                                ) === 'secondary' &&
                                                    'bg-muted text-muted-foreground',
                                            ]"
                                        >
                                            {{ activity.event ?? '—' }}
                                        </span>
                                    </TableCell>
                                    <TableCell
                                        class="text-sm text-muted-foreground"
                                    >
                                        <span v-if="activity.subject_type">
                                            {{ activity.subject_type }} #{{
                                                activity.subject_id
                                            }}
                                        </span>
                                        <span v-else>—</span>
                                    </TableCell>
                                    <TableCell
                                        class="text-xs text-muted-foreground"
                                    >
                                        {{ activity.log_name ?? 'default' }}
                                    </TableCell>
                                    <TableCell class="flex items-center gap-2">
                                        <Button
                                            v-if="canSuspendCauser(activity)"
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 gap-1 text-destructive hover:bg-destructive/10 hover:text-destructive"
                                            :disabled="
                                                suspendingCauserId ===
                                                `${activity.causer_type_full}-${activity.causer_id}`
                                            "
                                            @click="suspendCauser(activity)"
                                        >
                                            <Loader2
                                                v-if="
                                                    suspendingCauserId ===
                                                    `${activity.causer_type_full}-${activity.causer_id}`
                                                "
                                                class="size-3.5 animate-spin"
                                            />
                                            <UserMinus
                                                v-else
                                                class="size-3.5"
                                            />
                                            Suspend
                                        </Button>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 gap-1"
                                            @click="
                                                openPropertiesModal(activity)
                                            "
                                        >
                                            <Box class="size-3.5" />
                                            Properties
                                        </Button>
                                        <Link
                                            :href="
                                                activityLogShow(activity.id).url
                                            "
                                            class="inline-flex items-center gap-1 text-sm text-primary hover:underline"
                                        >
                                            View
                                            <ExternalLink class="size-3.5" />
                                        </Link>
                                    </TableCell>
                                </TableRow>
                            </template>
                        </template>
                        <template v-else>
                            <TableRow
                                v-for="activity in activities.data"
                                :key="activity.id"
                            >
                                <TableCell
                                    class="text-sm whitespace-nowrap text-muted-foreground"
                                >
                                    {{ formatDate(activity.created_at) }}
                                </TableCell>
                                <TableCell>
                                    <span
                                        :class="[
                                            'inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium',
                                            eventBadgeVariant(
                                                activity.event,
                                            ) === 'default' &&
                                                'bg-primary text-primary-foreground',
                                            eventBadgeVariant(
                                                activity.event,
                                            ) === 'outline' &&
                                                'border border-input bg-background',
                                            eventBadgeVariant(
                                                activity.event,
                                            ) === 'destructive' &&
                                                'bg-destructive/10 text-destructive',
                                            eventBadgeVariant(
                                                activity.event,
                                            ) === 'secondary' &&
                                                'bg-muted text-muted-foreground',
                                        ]"
                                    >
                                        {{ activity.event ?? '—' }}
                                    </span>
                                </TableCell>
                                <TableCell
                                    class="text-sm text-muted-foreground"
                                >
                                    <span v-if="activity.subject_type">
                                        {{ activity.subject_type }} #{{
                                            activity.subject_id
                                        }}
                                    </span>
                                    <span v-else>—</span>
                                </TableCell>
                                <TableCell
                                    class="text-sm text-muted-foreground"
                                >
                                    <span
                                        v-if="activity.causer_email"
                                        class="group flex cursor-pointer items-center gap-1.5 rounded px-1.5 py-0.5 transition-colors hover:bg-muted/80"
                                        :title="`Copy ${activity.causer_email}`"
                                        @click="
                                            copyCauserEmail(
                                                activity.causer_email,
                                            )
                                        "
                                    >
                                        <span class="truncate">{{
                                            activity.causer_email
                                        }}</span>
                                        <Copy
                                            class="size-3.5 shrink-0 opacity-60 group-hover:opacity-100"
                                            :class="{
                                                'text-green-600 dark:text-green-400':
                                                    copiedEmail ===
                                                    activity.causer_email,
                                            }"
                                        />
                                    </span>
                                    <span v-else>—</span>
                                </TableCell>
                                <TableCell
                                    class="text-xs text-muted-foreground"
                                >
                                    {{ activity.log_name ?? 'default' }}
                                </TableCell>
                                <TableCell class="flex items-center gap-2">
                                    <Button
                                        v-if="canSuspendCauser(activity)"
                                        variant="ghost"
                                        size="sm"
                                        class="h-8 gap-1 text-destructive hover:bg-destructive/10 hover:text-destructive"
                                        :disabled="
                                            suspendingCauserId ===
                                            `${activity.causer_type_full}-${activity.causer_id}`
                                        "
                                        @click="suspendCauser(activity)"
                                    >
                                        <Loader2
                                            v-if="
                                                suspendingCauserId ===
                                                `${activity.causer_type_full}-${activity.causer_id}`
                                            "
                                            class="size-3.5 animate-spin"
                                        />
                                        <UserMinus v-else class="size-3.5" />
                                        Suspend
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="h-8 gap-1"
                                        @click="openPropertiesModal(activity)"
                                    >
                                        <Box class="size-3.5" />
                                        Properties
                                    </Button>
                                    <Link
                                        :href="activityLogShow(activity.id).url"
                                        class="inline-flex items-center gap-1 text-sm text-primary hover:underline"
                                    >
                                        View
                                        <ExternalLink class="size-3.5" />
                                    </Link>
                                </TableCell>
                            </TableRow>
                        </template>
                    </TableBody>
                </Table>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center rounded-xl border border-dashed py-12"
            >
                <ClipboardList class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No activity yet</h3>
                <p class="text-center text-sm text-muted-foreground">
                    {{
                        (filters.search ?? '')
                            ? 'No entries match your search.'
                            : 'Activity will appear here as models are changed.'
                    }}
                </p>
            </div>

            <Pagination
                v-if="activities.links?.length"
                :links="activities.links"
            />

            <Dialog :open="modalOpen" @update:open="modalOpen = $event">
                <DialogContent class="sm:max-w-2xl">
                    <DialogHeader>
                        <DialogTitle>
                            Properties (attributes / old values)
                        </DialogTitle>
                    </DialogHeader>
                    <div class="mt-2 max-h-[420px] overflow-auto">
                        <div
                            v-if="modalLoading"
                            class="flex items-center justify-center gap-2 py-12 text-muted-foreground"
                        >
                            <Loader2 class="size-5 animate-spin" />
                            <span>Loading…</span>
                        </div>
                        <Table v-else-if="hasModalProperties">
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[140px]"
                                        >Property</TableHead
                                    >
                                    <TableHead>Old value</TableHead>
                                    <TableHead>New value</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-for="row in modalPropertiesRows"
                                    :key="row.property"
                                >
                                    <TableCell class="font-medium">
                                        {{ row.property }}
                                    </TableCell>
                                    <TableCell
                                        class="font-mono text-xs text-muted-foreground"
                                    >
                                        {{ formatPropertyValue(row.oldValue) }}
                                    </TableCell>
                                    <TableCell class="font-mono text-xs">
                                        {{ formatPropertyValue(row.newValue) }}
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                        <p
                            v-else
                            class="py-8 text-center text-sm text-muted-foreground"
                        >
                            No properties recorded for this activity.
                        </p>
                    </div>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
