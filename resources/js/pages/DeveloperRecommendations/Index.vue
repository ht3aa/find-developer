<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { ExternalLink, MoreHorizontal, Pencil, ThumbsUp, Trash2 } from 'lucide-vue-next';
import DeveloperRecommendationController from '@/actions/App/Http/Controllers/Dashboard/DeveloperRecommendationController';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import Pagination from '@/components/Pagination.vue';
import {
    index as developerRecommendationsIndex,
} from '@/routes/developer-recommendations';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

type RecommendationRow = {
    id: number;
    recommender_id: number;
    recommender_name: string | null;
    recommender_slug: string | null;
    recommended_id: number;
    recommended_name: string | null;
    recommended_slug: string | null;
    recommendation_note: string | null;
    status: string;
    status_label: string;
    created_at: string;
    updated_at: string;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatedRecommendations = {
    data: RecommendationRow[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
};

type StatusOption = { value: string; label: string };

type Props = {
    recommendations: PaginatedRecommendations;
    filters?: { status?: string };
    statusOptions: StatusOption[];
};

const props = withDefaults(defineProps<Props>(), {
    filters: () => ({ status: '' }),
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Recommendations', href: developerRecommendationsIndex().url },
];

const statusFilter = ref(props.filters.status ?? '');

watch(statusFilter, (value) => {
    router.get(developerRecommendationsIndex().url, { status: value || undefined }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});

const page = usePage();
const flash = (page.props.flash as { success?: string; error?: string } | undefined) ?? {};

function confirmDelete(r: RecommendationRow): void {
    if (window.confirm('Are you sure you want to delete this recommendation?')) {
        router.delete(DeveloperRecommendationController.destroy.url(r.id), { preserveScroll: true });
    }
}

function statusVariant(status: string): 'default' | 'secondary' | 'destructive' | 'outline' {
    if (status === 'approved') return 'default';
    if (status === 'rejected') return 'destructive';
    if (status === 'processing') return 'secondary';
    return 'outline';
}

function formatDate(iso: string): string {
    return new Date(iso).toLocaleString(undefined, { dateStyle: 'short', timeStyle: 'short' });
}

function developerUrl(slug: string | null): string {
    if (!slug) return '#';
    return `/developers/${slug}`;
}
</script>

<template>
    <Head title="Recommendations" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div
                v-if="flash.success"
                class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:border-green-800 dark:bg-green-950/50 dark:text-green-200"
            >
                {{ flash.success }}
            </div>
            <div
                v-if="flash.error"
                class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 dark:border-red-800 dark:bg-red-950/50 dark:text-red-200"
            >
                {{ flash.error }}
            </div>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Recommendations
                    </h1>
                    <p class="text-muted-foreground">
                        Manage developer recommendations (super admin only)
                    </p>
                </div>
            </div>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="sm:max-w-[200px]">
                    <label for="status-filter" class="sr-only">Status</label>
                    <select
                        id="status-filter"
                        v-model="statusFilter"
                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    >
                        <option value="">
                            All statuses
                        </option>
                        <option
                            v-for="opt in statusOptions"
                            :key="opt.value"
                            :value="opt.value"
                        >
                            {{ opt.label }}
                        </option>
                    </select>
                </div>
                <p
                    v-if="recommendations.total > 0"
                    class="text-sm text-muted-foreground"
                >
                    Showing {{ recommendations.from }}–{{ recommendations.to }} of {{ recommendations.total }}
                </p>
            </div>

            <div
                v-if="recommendations.data.length > 0"
                class="rounded-md border"
            >
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[140px]">Recommender</TableHead>
                            <TableHead class="w-[140px]">Recommended</TableHead>
                            <TableHead class="max-w-[200px]">Note</TableHead>
                            <TableHead class="w-[100px]">Status</TableHead>
                            <TableHead class="w-[140px]">Created</TableHead>
                            <TableHead class="w-[80px]" />
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="r in recommendations.data"
                            :key="r.id"
                        >
                            <TableCell class="text-sm">
                                <a
                                    v-if="r.recommender_slug"
                                    :href="developerUrl(r.recommender_slug)"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1 text-primary hover:underline"
                                >
                                    {{ r.recommender_name ?? '—' }}
                                    <ExternalLink class="size-3.5" />
                                </a>
                                <span v-else>{{ r.recommender_name ?? '—' }}</span>
                            </TableCell>
                            <TableCell class="text-sm">
                                <a
                                    v-if="r.recommended_slug"
                                    :href="developerUrl(r.recommended_slug)"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1 text-primary hover:underline"
                                >
                                    {{ r.recommended_name ?? '—' }}
                                    <ExternalLink class="size-3.5" />
                                </a>
                                <span v-else>{{ r.recommended_name ?? '—' }}</span>
                            </TableCell>
                            <TableCell class="max-w-[200px] truncate text-muted-foreground text-sm" :title="r.recommendation_note ?? ''">
                                {{ r.recommendation_note ?? '—' }}
                            </TableCell>
                            <TableCell>
                                <Badge :variant="statusVariant(r.status)">
                                    {{ r.status_label }}
                                </Badge>
                            </TableCell>
                            <TableCell class="text-muted-foreground text-sm whitespace-nowrap">
                                {{ formatDate(r.created_at) }}
                            </TableCell>
                            <TableCell>
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" size="icon" class="h-8 w-8">
                                            <span class="sr-only">Open menu</span>
                                            <MoreHorizontal class="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem as-child>
                                            <Link :href="DeveloperRecommendationController.edit.url(r.id)">
                                                <Pencil class="mr-2 h-4 w-4" />
                                                Edit
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            class="text-destructive focus:text-destructive"
                                            @click="confirmDelete(r)"
                                        >
                                            <Trash2 class="mr-2 h-4 w-4" />
                                            Delete
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center rounded-xl border border-dashed py-12"
            >
                <ThumbsUp class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No recommendations yet</h3>
                <p class="text-center text-sm text-muted-foreground">
                    {{ statusFilter ? 'No recommendations match the selected status.' : 'Developer recommendations will appear here once submitted.' }}
                </p>
            </div>

            <Pagination
                v-if="recommendations.links?.length"
                :links="recommendations.links"
            />
        </div>
    </AppLayout>
</template>
