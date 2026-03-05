<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    Check,
    Eye,
    MoreHorizontal,
    Send,
    Trash2,
    X,
} from 'lucide-vue-next';
import { ref, watch } from 'vue';
import DeveloperOfferController from '@/actions/App/Http/Controllers/Dashboard/DeveloperOfferController';
import Pagination from '@/components/Pagination.vue';
import { Badge } from '@/components/ui/badge';
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
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as developerOffersIndex } from '@/routes/developer-offers';
import type { BreadcrumbItem } from '@/types';

type OfferRow = {
    id: number;
    developer_ids: number[];
    developer_names: string[];
    company_name: string;
    job_title_name: string | null;
    message: string;
    salary_range: string | null;
    work_type: string | null;
    contact_email: string;
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

type PaginatedOffers = {
    data: OfferRow[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
};

type StatusOption = { value: string; label: string };

type Props = {
    offers: PaginatedOffers;
    filters?: { status?: string };
    statusOptions: StatusOption[];
};

const props = withDefaults(defineProps<Props>(), {
    filters: () => ({ status: '' }),
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Developer Offers', href: developerOffersIndex().url },
];

const statusFilter = ref(props.filters.status ?? '');

watch(statusFilter, (value) => {
    router.get(
        developerOffersIndex().url,
        { status: value || undefined },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
});

const page = usePage();
const flash =
    (page.props.flash as { success?: string; error?: string } | undefined) ??
    {};

function confirmDelete(o: OfferRow): void {
    if (window.confirm('Are you sure you want to delete this offer?')) {
        router.delete(DeveloperOfferController.destroy.url(o.id), {
            preserveScroll: true,
        });
    }
}

function setStatus(o: OfferRow, status: string): void {
    router.put(DeveloperOfferController.update.url(o.id), { status }, {
        preserveScroll: true,
    });
}

function statusVariant(
    status: string,
): 'default' | 'secondary' | 'destructive' | 'outline' {
    if (status === 'approved') return 'default';
    if (status === 'rejected') return 'destructive';
    return 'outline';
}

function formatDate(iso: string): string {
    return new Date(iso).toLocaleString(undefined, {
        dateStyle: 'short',
        timeStyle: 'short',
    });
}
</script>

<template>
    <Head title="Developer Offers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
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

            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Developer Offers
                    </h1>
                    <p class="text-muted-foreground">
                        Manage developer offers (super admin only)
                    </p>
                </div>
            </div>

            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="sm:max-w-[200px]">
                    <label for="status-filter" class="sr-only">Status</label>
                    <select
                        id="status-filter"
                        v-model="statusFilter"
                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                    >
                        <option value="">All statuses</option>
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
                    v-if="offers.total > 0"
                    class="text-sm text-muted-foreground"
                >
                    Showing {{ offers.from }}–{{ offers.to }}
                    of {{ offers.total }}
                </p>
            </div>

            <div
                v-if="offers.data.length > 0"
                class="rounded-md border"
            >
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[140px]">Developers</TableHead>
                            <TableHead class="w-[120px]">Company</TableHead>
                            <TableHead class="w-[120px]">Position</TableHead>
                            <TableHead class="max-w-[180px]">Message</TableHead>
                            <TableHead class="w-[100px]">Status</TableHead>
                            <TableHead class="w-[140px]">Created</TableHead>
                            <TableHead class="w-[120px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="o in offers.data" :key="o.id">
                            <TableCell class="text-sm">
                                <span class="line-clamp-2">
                                    {{ o.developer_names?.join(', ') ?? '—' }}
                                </span>
                            </TableCell>
                            <TableCell class="text-sm">
                                <Link
                                    :href="
                                        DeveloperOfferController.show.url(o.id)
                                    "
                                    class="font-medium text-primary hover:underline"
                                >
                                    {{ o.company_name ?? '—' }}
                                </Link>
                            </TableCell>
                            <TableCell class="text-sm">
                                {{ o.job_title_name ?? '—' }}
                            </TableCell>
                            <TableCell
                                class="max-w-[180px] truncate text-sm text-muted-foreground"
                                :title="o.message ?? ''"
                            >
                                {{ o.message ?? '—' }}
                            </TableCell>
                            <TableCell>
                                <Badge :variant="statusVariant(o.status)">
                                    {{ o.status_label }}
                                </Badge>
                            </TableCell>
                            <TableCell
                                class="text-sm whitespace-nowrap text-muted-foreground"
                            >
                                {{ formatDate(o.created_at) }}
                            </TableCell>
                            <TableCell>
                                <div
                                    v-if="o.status === 'pending'"
                                    class="flex items-center gap-1"
                                >
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        class="h-7 gap-1"
                                        as-child
                                    >
                                        <Link
                                            :href="
                                                DeveloperOfferController.show.url(
                                                    o.id,
                                                )
                                            "
                                        >
                                            <Eye class="size-3.5" />
                                            View
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        class="h-7 gap-1 text-green-600 hover:bg-green-50 hover:text-green-700 dark:text-green-400 dark:hover:bg-green-950/50"
                                        @click="setStatus(o, 'approved')"
                                    >
                                        <Check class="size-3.5" />
                                        Approve
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        class="h-7 gap-1 text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-950/50"
                                        @click="setStatus(o, 'rejected')"
                                    >
                                        <X class="size-3.5" />
                                        Reject
                                    </Button>
                                </div>
                                <DropdownMenu v-else>
                                    <DropdownMenuTrigger as-child>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="h-8 w-8"
                                        >
                                            <span class="sr-only"
                                                >Open menu</span
                                            >
                                            <MoreHorizontal class="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem as-child>
                                            <Link
                                                :href="
                                                    DeveloperOfferController.show.url(
                                                        o.id,
                                                    )
                                                "
                                            >
                                                <Eye class="mr-2 h-4 w-4" />
                                                View
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            v-if="o.status !== 'approved'"
                                            @click="setStatus(o, 'approved')"
                                        >
                                            <Check class="mr-2 h-4 w-4" />
                                            Approve
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            v-if="o.status !== 'rejected'"
                                            @click="setStatus(o, 'rejected')"
                                        >
                                            <X class="mr-2 h-4 w-4" />
                                            Reject
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            class="text-destructive focus:text-destructive"
                                            @click="confirmDelete(o)"
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
                <Send class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">
                    No offers yet
                </h3>
                <p class="text-center text-sm text-muted-foreground">
                    {{
                        statusFilter
                            ? 'No offers match the selected status.'
                            : 'Developer offers will appear here once submitted from the home page.'
                    }}
                </p>
            </div>

            <Pagination
                v-if="offers.links?.length"
                :links="offers.links"
            />
        </div>
    </AppLayout>
</template>
