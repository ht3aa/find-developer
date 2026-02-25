<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Briefcase, Plus } from 'lucide-vue-next';
import WorkExperienceController from '@/actions/App/Http/Controllers/Dashboard/WorkExperienceController';
import WorkExperienceDataTable from '@/components/work-experience/WorkExperienceDataTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { index as workExperienceIndex, create as workExperienceCreate } from '@/routes/work-experience';
import { dashboard } from '@/routes';
import type { WorkExperience } from '@/types/work-experience';
import type { BreadcrumbItem } from '@/types';

type Props = {
    workExperiences: WorkExperience[];
};

defineProps<Props>();

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string; error?: string } | undefined);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Work Experience', href: workExperienceIndex().url },
];

function confirmDelete(workExperience: WorkExperience) {
    if (
        window.confirm(
            `Are you sure you want to delete your experience at "${workExperience.company_name}"?`,
        )
    ) {
        router.delete(WorkExperienceController.destroy.url(workExperience.id), {
            preserveScroll: true,
        });
    }
}
</script>

<template>
    <Head title="Work Experience" />

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
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Work Experience
                    </h1>
                    <p class="text-muted-foreground">
                        Manage your work history and professional experience
                    </p>
                </div>
                <Button as-child>
                    <Link :href="workExperienceCreate().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Experience
                    </Link>
                </Button>
            </div>

            <WorkExperienceDataTable
                v-if="workExperiences.length > 0"
                :data="workExperiences"
                :on-delete="confirmDelete"
            />

            <div
                v-else
                class="flex flex-col items-center justify-center rounded-xl border border-dashed py-12"
            >
                <Briefcase class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No work experience yet</h3>
                <p class="mb-4 text-center text-sm text-muted-foreground">
                    Add your first work experience to showcase your professional
                    background.
                </p>
                <Button as-child>
                    <Link :href="workExperienceCreate().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Experience
                    </Link>
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
