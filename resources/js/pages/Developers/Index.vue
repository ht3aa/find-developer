<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { refDebounced } from '@vueuse/core';
import { computed, ref, watch } from 'vue';
import { Plus, Search, Users } from 'lucide-vue-next';
import DevelopersDataTable from '@/components/developers/DevelopersDataTable.vue';
import Pagination from '@/components/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { create as developersCreate, index as developersIndex } from '@/routes/developers';
import { dashboard } from '@/routes';
import type { DeveloperTableRow } from '@/types/developer-table';
import type { BreadcrumbItem } from '@/types';

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatedDevelopers = {
    data: DeveloperTableRow[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
};

type Props = {
    developers: PaginatedDevelopers;
    filters?: { search?: string; status?: string };
};

const props = withDefaults(defineProps<Props>(), {
    filters: () => ({ search: '', status: '' }),
});

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string; error?: string } | undefined);

const searchQuery = ref(props.filters.search ?? '');
const statusQuery = ref(props.filters.status ?? 'all');
const debouncedSearch = refDebounced(searchQuery, 300);

watch(debouncedSearch, (value) => {
    router.get(developersIndex().url, { search: value || undefined, status: statusQuery.value || undefined }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});

watch(statusQuery, (value) => {
    router.get(developersIndex().url, { status: value || undefined, search: debouncedSearch.value || undefined }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Developers', href: developersIndex().url },
];

const statuses = [
    { value: '', label: 'All Statuses' },
    { value: 'pending', label: 'Pending' },
    { value: 'approved', label: 'Approved' },
    { value: 'rejected', label: 'Rejected' },
    { value: 'experience_changed', label: 'Experience Changed' },
];
</script>

<template>
    <Head title="Developers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div
                v-if="flash?.success"
                class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:border-green-800 dark:bg-green-950/50 dark:text-green-200"
            >
                {{ flash.success }}
            </div>
            <div
                v-if="flash?.error"
                class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 dark:border-red-800 dark:bg-red-950/50 dark:text-red-200"
            >
                {{ flash.error }}
            </div>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Developers</h1>
                    <p class="text-muted-foreground">
                        View and manage all developers in the platform
                    </p>
                </div>
                <Button as-child>
                    <Link :href="developersCreate().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Developer
                    </Link>
                </Button>
            </div>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-1 gap-2 sm:max-w-md">
                    <div class="relative flex-1">
                        <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            v-model="searchQuery"
                            type="search"
                            placeholder="Search by name, email..."
                            class="pl-9"
                        />
                    </div>
                    <select
                        v-model="statusQuery"
                        class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 sm:w-[180px]"
                    >
                        <option value="" disabled>Select status</option>
                        <option
                            v-for="status in statuses"
                            :key="status.value"
                            :value="status.value"
                        >
                            {{ status.label }}
                        </option>
                    </select>
                </div>
                <p
                    v-if="developers.total > 0"
                    class="text-sm text-muted-foreground"
                >
                    Showing {{ developers.from }}–{{ developers.to }} of {{ developers.total }}
                </p>
            </div>

            <DevelopersDataTable
                v-if="developers.data.length > 0"
                :data="developers.data"
            />

            <div
                v-else-if="!(filters.search ?? '')"
                class="flex flex-col items-center justify-center rounded-xl border border-dashed py-12"
            >
                <Users class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No developers yet</h3>
                <p class="mb-4 text-center text-sm text-muted-foreground">
                    Get started by adding your first developer.
                </p>
                <Button as-child>
                    <Link :href="developersCreate().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Developer
                    </Link>
                </Button>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center rounded-xl border border-dashed py-12"
            >
                <Search class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No developers found</h3>
                <p class="text-center text-sm text-muted-foreground">
                    Try adjusting your search.
                </p>
            </div>

            <Pagination v-if="developers.links?.length" :links="developers.links" />
        </div>
    </AppLayout>
</template>
