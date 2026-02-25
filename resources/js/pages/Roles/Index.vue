<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Shield } from 'lucide-vue-next';
import RoleController from '@/actions/App/Http/Controllers/Dashboard/RoleController';
import RolesDataTable from '@/components/roles/RolesDataTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { index as rolesIndex, create as rolesCreate } from '@/routes/roles';
import { dashboard } from '@/routes';
import type { Role } from '@/types/role';
import type { BreadcrumbItem } from '@/types';

type Props = {
    roles: Role[];
};

const props = defineProps<Props>();

function confirmDelete(role: Role) {
    if (window.confirm(`Are you sure you want to delete the role "${role.name}"?`)) {
        router.delete(RoleController.destroy.url(role.id), {
            preserveScroll: true,
        });
    }
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Roles', href: rolesIndex().url },
];
</script>

<template>
    <Head title="Roles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Roles</h1>
                    <p class="text-muted-foreground">
                        Create and manage roles to assign to users
                    </p>
                </div>
                <Button as-child>
                    <Link :href="rolesCreate().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Role
                    </Link>
                </Button>
            </div>

            <RolesDataTable
                v-if="roles.length > 0"
                :data="roles"
                :on-delete="confirmDelete"
            />

            <div
                v-else
                class="flex flex-col items-center justify-center rounded-xl border border-dashed py-12"
            >
                <Shield class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No roles yet</h3>
                <p class="mb-4 text-center text-sm text-muted-foreground">
                    Create roles to assign to users (e.g. Admin, Editor).
                </p>
                <Button as-child>
                    <Link :href="rolesCreate().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Role
                    </Link>
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
