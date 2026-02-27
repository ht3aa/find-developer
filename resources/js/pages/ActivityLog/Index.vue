<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { refDebounced } from '@vueuse/core';
import { computed, ref, watch } from 'vue';
import { Box, ClipboardList, Copy, ExternalLink, Loader2, Search } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
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
import Pagination from '@/components/Pagination.vue';
import { index as activityLogIndex, show as activityLogShow } from '@/routes/dashboard/activity-log';
import { dashboard } from '@/routes';
import type { ActivityLogEntry } from '@/types/activity-log';
import type { BreadcrumbItem } from '@/types';

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

type Props = {
    activities: PaginatedActivities;
    filters?: { search?: string; log_name?: string };
};

const props = withDefaults(defineProps<Props>(), {
    filters: () => ({ search: '', log_name: '' }),
});

const page = usePage();
const searchQuery = ref(props.filters.search ?? '');
const debouncedSearch = refDebounced(searchQuery, 300);
const copiedEmail = ref<string | null>(null);
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
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
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
        .map(([key, value]) => ({ property: key, oldValue: undefined, newValue: value }));
});

const hasModalProperties = computed(
    () => modalPropertiesRows.value.length > 0,
);

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

watch(debouncedSearch, (value) => {
    router.get(activityLogIndex().url, { search: value || undefined, log_name: props.filters.log_name || undefined }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});

function formatDate(iso: string): string {
    const d = new Date(iso);
    return d.toLocaleString(undefined, {
        dateStyle: 'short',
        timeStyle: 'short',
    });
}

function eventBadgeVariant(event: string | null): 'default' | 'secondary' | 'destructive' | 'outline' {
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
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Activity Log
                    </h1>
                    <p class="text-muted-foreground">
                        Audit trail of model changes (super admin only)
                    </p>
                </div>
            </div>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="relative flex-1 sm:max-w-sm">
                    <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Search description, subject, event..."
                        class="pl-9"
                    />
                </div>
                <p
                    v-if="activities.total > 0"
                    class="text-sm text-muted-foreground"
                >
                    Showing {{ activities.from }}–{{ activities.to }} of {{ activities.total }}
                </p>
            </div>

            <div
                v-if="activities.data.length > 0"
                class="rounded-md border"
            >
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[140px]">Date</TableHead>
                            <TableHead class="w-[90px]">Event</TableHead>
                            <TableHead class="w-[120px]">Subject</TableHead>
                            <TableHead class="w-[180px]">Causer email</TableHead>
                            <TableHead class="w-[80px]">Log</TableHead>
                            <TableHead class="w-[160px]" />
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="activity in activities.data"
                            :key="activity.id"
                        >
                            <TableCell class="text-muted-foreground text-sm whitespace-nowrap">
                                {{ formatDate(activity.created_at) }}
                            </TableCell>
                            <TableCell>
                                <span
                                    :class="[
                                        'inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium',
                                        eventBadgeVariant(activity.event) === 'default' && 'bg-primary text-primary-foreground',
                                        eventBadgeVariant(activity.event) === 'outline' && 'border border-input bg-background',
                                        eventBadgeVariant(activity.event) === 'destructive' && 'bg-destructive/10 text-destructive',
                                        eventBadgeVariant(activity.event) === 'secondary' && 'bg-muted text-muted-foreground',
                                    ]"
                                >
                                    {{ activity.event ?? '—' }}
                                </span>
                            </TableCell>
                            <TableCell class="text-muted-foreground text-sm">
                                <span v-if="activity.subject_type">
                                    {{ activity.subject_type }} #{{ activity.subject_id }}
                                </span>
                                <span v-else>—</span>
                            </TableCell>
                            <TableCell class="text-muted-foreground text-sm">
                                <span
                                    v-if="activity.causer_email"
                                    class="group flex cursor-pointer items-center gap-1.5 rounded px-1.5 py-0.5 transition-colors hover:bg-muted/80"
                                    :title="`Copy ${activity.causer_email}`"
                                    @click="copyCauserEmail(activity.causer_email)"
                                >
                                    <span class="truncate">{{ activity.causer_email }}</span>
                                    <Copy
                                        class="size-3.5 shrink-0 opacity-60 group-hover:opacity-100"
                                        :class="{ 'text-green-600 dark:text-green-400': copiedEmail === activity.causer_email }"
                                    />
                                </span>
                                <span v-else>—</span>
                            </TableCell>
                            <TableCell class="text-muted-foreground text-xs">
                                {{ activity.log_name ?? 'default' }}
                            </TableCell>
                            <TableCell class="flex items-center gap-2">
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
                    {{ (filters.search ?? '') ? 'No entries match your search.' : 'Activity will appear here as models are changed.' }}
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
                                    <TableHead class="w-[140px]">Property</TableHead>
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
                                    <TableCell class="font-mono text-muted-foreground text-xs">
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
