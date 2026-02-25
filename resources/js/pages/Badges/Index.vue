<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Award, Plus } from 'lucide-vue-next';
import BadgeController from '@/actions/App/Http/Controllers/BadgeController';
import BadgesDataTable from '@/components/badges/BadgesDataTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { index as badgesIndex, create } from '@/routes/badges';
import { dashboard } from '@/routes';
import type { Badge as BadgeType } from '@/types/badge';
import type { BreadcrumbItem } from '@/types';

type Props = {
    badges: BadgeType[];
};

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Badges', href: badgesIndex().url },
];

function confirmDelete(badge: BadgeType) {
    if (window.confirm(`Are you sure you want to delete "${badge.name}"?`)) {
        router.delete(BadgeController.destroy.url(badge.id), {
            preserveScroll: true,
        });
    }
}
</script>

<template>
    <Head title="Badges" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Badges</h1>
                    <p class="text-muted-foreground">
                        Manage badges that can be assigned to developers
                    </p>
                </div>
                <Button as-child>
                    <Link :href="create().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Badge
                    </Link>
                </Button>
            </div>

            <BadgesDataTable
                v-if="badges.length > 0"
                :data="badges"
                :on-delete="confirmDelete"
            />

            <div
                v-else
                class="flex flex-col items-center justify-center rounded-xl border border-dashed py-12"
            >
                <Award class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No badges yet</h3>
                <p class="mb-4 text-center text-sm text-muted-foreground">
                    Get started by creating your first badge.
                </p>
                <Button as-child>
                    <Link :href="create().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Badge
                    </Link>
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
