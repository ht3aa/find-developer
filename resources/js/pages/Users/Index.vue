<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { refDebounced } from '@vueuse/core';
import { computed, ref, watch } from 'vue';
import { Plus, Search, UserCog } from 'lucide-vue-next';
import UserController from '@/actions/App/Http/Controllers/Dashboard/UserController';
import Pagination from '@/components/Pagination.vue';
import UsersDataTable from '@/components/users/UsersDataTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { create as usersCreate, index as usersIndex } from '@/routes/users';
import { dashboard } from '@/routes';
import type { UserTableRow } from '@/types/user';
import type { BreadcrumbItem } from '@/types';

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatedUsers = {
    data: UserTableRow[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
};

type Props = {
    users: PaginatedUsers;
    filters?: { search?: string };
};

const props = withDefaults(defineProps<Props>(), {
    filters: () => ({ search: '' }),
});

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string; error?: string } | undefined);

const searchQuery = ref(props.filters.search ?? '');
const debouncedSearch = refDebounced(searchQuery, 300);

watch(debouncedSearch, (value) => {
    router.get(usersIndex().url, { search: value || undefined }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});

function confirmDelete(user: UserTableRow) {
    if (window.confirm(`Are you sure you want to delete "${user.name}"?`)) {
        router.delete(UserController.destroy.url(user.id), {
            preserveScroll: true,
        });
    }
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Users', href: usersIndex().url },
];
</script>

<template>
    <Head title="Users" />

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
                    <h1 class="text-2xl font-semibold tracking-tight">Users</h1>
                    <p class="text-muted-foreground">
                        Manage users and their roles
                    </p>
                </div>
                <Button as-child>
                    <Link :href="usersCreate().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Add User
                    </Link>
                </Button>
            </div>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="relative flex-1 sm:max-w-sm">
                    <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Search by name, email..."
                        class="pl-9"
                    />
                </div>
                <p
                    v-if="users.total > 0"
                    class="text-sm text-muted-foreground"
                >
                    Showing {{ users.from }}–{{ users.to }} of {{ users.total }}
                </p>
            </div>

            <UsersDataTable
                v-if="users.data.length > 0"
                :data="users.data"
                :on-delete="confirmDelete"
            />

            <div
                v-else-if="!(filters.search ?? '')"
                class="flex flex-col items-center justify-center rounded-xl border border-dashed py-12"
            >
                <UserCog class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No users yet</h3>
                <p class="mb-4 text-center text-sm text-muted-foreground">
                    Get started by creating your first user.
                </p>
                <Button as-child>
                    <Link :href="usersCreate().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Add User
                    </Link>
                </Button>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center rounded-xl border border-dashed py-12"
            >
                <Search class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No users found</h3>
                <p class="text-center text-sm text-muted-foreground">
                    Try adjusting your search.
                </p>
            </div>

            <Pagination v-if="users.links?.length" :links="users.links" />
        </div>
    </AppLayout>
</template>
