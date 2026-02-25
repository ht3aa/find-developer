<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Award, UserCog, Users } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index as badgesIndex } from '@/routes/badges';
import { index as developersIndex } from '@/routes/developers';
import usersRoutes from '@/routes/users';
import { type BreadcrumbItem } from '@/types';

type DashboardStats = {
    developers: number;
    users: number;
    badges: number;
};

const page = usePage();
const flashSuccess = computed(() => (page.props.flash as { success?: string })?.success);
const stats = computed<DashboardStats>(() => (page.props.stats as DashboardStats) ?? { developers: 0, users: 0, badges: 0 });

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const statCards = computed(() => [
    {
        title: 'Developers',
        value: stats.value.developers,
        icon: Users,
        description: 'Total developers in the platform',
    },
    {
        title: 'Users',
        value: stats.value.users,
        icon: UserCog,
        description: 'Registered user accounts',
    },
    {
        title: 'Badges',
        value: stats.value.badges,
        icon: Award,
        description: 'Badges available for assignment',
    },
]);
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div
                v-if="flashSuccess"
                class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:border-green-800 dark:bg-green-950/50 dark:text-green-200"
            >
                {{ flashSuccess }}
            </div>
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <Link
                    v-for="card in statCards"
                    :key="card.title"
                    :href="card.href"
                    class="focus:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 rounded-xl"
                >
                    <Card
                        class="transition-colors hover:bg-accent/50 dark:hover:bg-accent/20"
                    >
                        <CardContent class="flex flex-row items-center gap-4 p-6">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                            >
                                <component
                                    :is="card.icon"
                                    class="size-6"
                                    aria-hidden
                                />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-muted-foreground">
                                    {{ card.title }}
                                </p>
                                <p class="mt-1 text-2xl font-semibold tabular-nums">
                                    {{ card.value }}
                                </p>
                                <p class="mt-0.5 truncate text-xs text-muted-foreground">
                                    {{ card.description }}
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
