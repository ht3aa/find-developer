<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { Users } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import DeveloperController from '@/actions/App/Http/Controllers/Dashboard/DeveloperController';
import DeveloperFormFields from '@/components/developers/DeveloperFormFields.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import {
    create as developersCreate,
    index as developersIndex,
} from '@/routes/developers';
import type { BreadcrumbItem } from '@/types';
import { parseSalaryForForm } from '@/utils/salary';

type UserOption = { id: number; name: string; email: string };
type JobTitleOption = { id: number; name: string };

type Props = {
    users: UserOption[];
    jobTitles: JobTitleOption[];
};

const props = defineProps<Props>();

const formData = ref<Record<string, unknown>>({
    user_id: null,
    name: '',
    email: '',
    phone: '',
    job_title: { name: '' },
    years_of_experience: 0,
    expected_salary_from: null,
    expected_salary_to: null,
    salary_currency: 'IQD',
    bio: '',
    portfolio_url: '',
    github_url: '',
    linkedin_url: '',
    youtube_url: '',
    is_available: false,
    availability_type: [],
    skills: [],
    badges: [],
    status: 'pending',
    recommended_by_us: false,
});

const formRef = ref<InstanceType<typeof DeveloperFormFields> | null>(null);
const submitting = ref(false);
const page = usePage();
const formErrors = computed(
    () => (page.props.errors as Record<string, string>) ?? {},
);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Developers', href: developersIndex().url },
    { title: 'Create', href: developersCreate().url },
];

function submit(): void {
    const d = formData.value;
    const jobTitle = props.jobTitles.find(
        (j) => j.name === (d.job_title as { name?: string })?.name,
    );
    const payload: Record<string, unknown> = {
        user_id: d.user_id,
        name: d.name ?? '',
        email: d.email ?? '',
        phone: d.phone ?? '',
        job_title_id: jobTitle?.id ?? null,
        years_of_experience: Number(d.years_of_experience) || 0,
        bio: d.bio ?? null,
        portfolio_url: d.portfolio_url ?? null,
        github_url: d.github_url ?? null,
        linkedin_url: d.linkedin_url ?? null,
        youtube_url: d.youtube_url ?? null,
        is_available: d.is_available ? 1 : 0,
        availability_type: (
            (d.availability_type as { value: string }[]) ?? []
        ).map((a) => a.value),
        skill_names: ((d.skills as { name: string }[]) ?? []).map(
            (s) => s.name,
        ),
        badge_names: ((d.badges as { name: string }[]) ?? []).map(
            (b) => b.name,
        ),
        status: d.status ?? 'pending',
        recommended_by_us: d.recommended_by_us ? 1 : 0,
        expected_salary_from: parseSalaryForForm(
            d.expected_salary_from as string | number | null | undefined,
        ),
        expected_salary_to: parseSalaryForForm(
            d.expected_salary_to as string | number | null | undefined,
        ),
        salary_currency: (d.salary_currency as string) ?? 'IQD',
    };
    const cvFile = formRef.value?.cvFile;
    const file =
        typeof cvFile === 'object' && cvFile && 'value' in cvFile
            ? cvFile.value
            : cvFile;
    if (file) payload.cv = file;
    submitting.value = true;
    router.post(DeveloperController.store.url(), payload, {
        forceFormData: !!file,
        preserveScroll: true,
        onSuccess: () => formRef.value?.clearCv?.(),
        onFinish: () => {
            submitting.value = false;
        },
    });
}
</script>

<template>
    <Head title="Create Developer" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full space-y-6 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10"
                >
                    <Users class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <Heading
                        title="Create Developer"
                        description="Add a new developer to the platform"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <Card v-if="users.length > 0" class="lg:col-span-2">
                    <CardHeader class="pb-4">
                        <h3 class="text-sm font-medium text-muted-foreground">
                            Developer details
                        </h3>
                    </CardHeader>
                    <CardContent>
                        <form class="space-y-6" @submit.prevent="submit">
                            <DeveloperFormFields
                                ref="formRef"
                                v-model="formData"
                                :errors="formErrors"
                                :job-titles="jobTitles"
                                :users="users"
                                show-user-select
                                show-admin-fields
                            />

                            <div class="flex flex-wrap items-center gap-3 pt-4">
                                <Button type="submit" :disabled="submitting">
                                    {{
                                        submitting
                                            ? 'Creating...'
                                            : 'Create Developer'
                                    }}
                                </Button>
                                <Button variant="outline" as-child>
                                    <a :href="developersIndex().url">Cancel</a>
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <Card v-else class="lg:col-span-2">
                    <CardContent class="py-12 text-center">
                        <p class="text-muted-foreground">
                            No users available. All registered users already
                            have developer profiles.
                        </p>
                        <Button variant="outline" class="mt-4" as-child>
                            <a :href="developersIndex().url"
                                >Back to Developers</a
                            >
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
