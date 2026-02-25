<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Award, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import BadgeController from '@/actions/App/Http/Controllers/BadgeController';
import AppLayout from '@/layouts/AppLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
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

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="badge in badges"
                    :key="badge.id"
                    class="flex flex-col"
                >
                    <CardHeader class="flex flex-row items-start justify-between space-y-0 pb-2">
                        <div class="flex items-center gap-2">
                            <Award class="h-5 w-5 text-muted-foreground" />
                            <CardTitle class="text-base">{{ badge.name }}</CardTitle>
                        </div>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon" class="h-8 w-8">
                                    <span class="sr-only">Open menu</span>
                                    <Pencil class="h-4 w-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuItem as-child>
                                    <Link :href="BadgeController.edit.url(badge.id)">
                                        Edit
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem
                                    class="text-destructive focus:text-destructive"
                                    @click="confirmDelete(badge)"
                                >
                                    <Trash2 class="mr-2 h-4 w-4" />
                                    Delete
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </CardHeader>
                    <CardContent class="flex-1">
                        <p
                            v-if="badge.description"
                            class="mb-3 text-sm text-muted-foreground line-clamp-2"
                        >
                            {{ badge.description }}
                        </p>
                        <div class="flex flex-wrap items-center gap-2">
                            <Badge
                                v-if="badge.color"
                                :style="{
                                    background: `${badge.color}18`,
                                    borderColor: `${badge.color}50`,
                                    color: badge.color,
                                }"
                                variant="outline"
                            >
                                {{ badge.color }}
                            </Badge>
                            <Badge v-else variant="secondary">
                                {{ badge.is_active ? 'Active' : 'Inactive' }}
                            </Badge>
                            <span class="text-xs text-muted-foreground">
                                {{ badge.developers_count ?? 0 }} developers
                            </span>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div
                v-if="badges.length === 0"
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
