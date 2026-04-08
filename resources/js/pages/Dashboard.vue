<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed, defineAsyncComponent } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

const AverageSalaryByExperienceChart = defineAsyncComponent(
    () => import('@/components/charts/AverageSalaryByExperienceChart.vue'),
);
const DevelopersByAvailabilityTypeChart = defineAsyncComponent(
    () => import('@/components/charts/DevelopersByAvailabilityTypeChart.vue'),
);
const DevelopersByJobTitleChart = defineAsyncComponent(
    () => import('@/components/charts/DevelopersByJobTitleChart.vue'),
);
const DevelopersByLocationChart = defineAsyncComponent(
    () => import('@/components/charts/DevelopersByLocationChart.vue'),
);

type LocationPoint = { label: string; count: number };
type AvailabilityPoint = { label: string; count: number };
type SalaryPoint = { years_of_experience: number; average_salary: number };
type JobTitlePoint = { label: string; count: number };

defineProps<{
    developersByLocation: LocationPoint[];
    developersByAvailabilityType: AvailabilityPoint[];
    averageSalaryByExperience: SalaryPoint[];
    developersByJobTitle: JobTitlePoint[];
}>();

const page = usePage();
const flashSuccess = computed(
    () => (page.props.flash as { success?: string })?.success,
);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
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
            <div class="grid gap-8 lg:grid-cols-2">
                <DevelopersByLocationChart :data="developersByLocation" />
                <DevelopersByAvailabilityTypeChart
                    :data="developersByAvailabilityType"
                />
                <AverageSalaryByExperienceChart
                    :data="averageSalaryByExperience"
                />
                <DevelopersByJobTitleChart :data="developersByJobTitle" />
            </div>
        </div>
    </AppLayout>
</template>
