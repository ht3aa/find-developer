<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { Users } from 'lucide-vue-next';
import { computed, reactive, ref, watch } from 'vue';
import DeveloperController from '@/actions/App/Http/Controllers/Dashboard/DeveloperController';
import DeveloperFormFields from '@/components/developers/DeveloperFormFields.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { edit as developersEdit, index as developersIndex } from '@/routes/developers';
import { dashboard } from '@/routes';
import type { Developer } from '@/types/developer';
import type { BreadcrumbItem } from '@/types';

type JobTitleOption = { id: number; name: string };
type UserOption = { id: number; name: string; email: string };

type Props = {
    developer: Developer;
    jobTitles: JobTitleOption[];
    users: UserOption[];
};

const props = defineProps<Props>();

const formData = ref<Record<string, unknown>>({
    user_id: null,
    name: '',
    email: '',
    phone: '',
    job_title: { name: '' },
    years_of_experience: 0,
    bio: '',
    portfolio_url: '',
    github_url: '',
    linkedin_url: '',
    youtube_url: '',
    is_available: false,
    availability_type: [],
    skills: [],
    status: 'pending',
    recommended_by_us: false,
    cv_path_url: null,
});

const formRef = ref<InstanceType<typeof DeveloperFormFields> | null>(null);
const submitting = ref(false);
const page = usePage();
const formErrors = computed(() => (page.props.errors as Record<string, string>) ?? {});
const flashSuccess = computed(() => (page.props.flash as { success?: string })?.success);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Developers', href: developersIndex().url },
    { title: props.developer.name, href: developersEdit(props.developer.id).url },
];

watch(
    () => props.developer,
    (dev) => {
        formData.value = reactive({
            user_id: dev.user_id ?? null,
            name: dev.name,
            email: dev.email,
            phone: dev.phone ?? '',
            job_title: dev.job_title ?? { name: '' },
            years_of_experience: dev.years_of_experience ?? 0,
            bio: dev.bio ?? '',
            portfolio_url: dev.portfolio_url ?? '',
            github_url: dev.github_url ?? '',
            linkedin_url: dev.linkedin_url ?? '',
            youtube_url: (dev as Record<string, unknown>).youtube_url ?? '',
            is_available: dev.is_available ?? false,
            availability_type: [...(dev.availability_type ?? [])],
            skills: [...(dev.skills ?? [])],
            status: (dev as Record<string, unknown>).status ?? 'pending',
            recommended_by_us: dev.recommended_by_us ?? false,
            cv_path_url: dev.cv_path_url ?? null,
        });
    },
    { immediate: true },
);

function submit(): void {
    const d = formData.value;
    const jobTitle = props.jobTitles.find(
        (j) => j.name === (d.job_title as { name?: string })?.name,
    );
    const payload: Record<string, unknown> = {
        user_id: d.user_id ?? null,
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
        is_available: d.is_available ?? false,
        availability_type: ((d.availability_type as { value: string }[]) ?? []).map((a) => a.value),
        skill_names: ((d.skills as { name: string }[]) ?? []).map((s) => s.name),
        status: d.status ?? 'pending',
        recommended_by_us: d.recommended_by_us ?? false,
    };
    const cvFile = formRef.value?.cvFile;
    const file = typeof cvFile === 'object' && cvFile && 'value' in cvFile ? cvFile.value : cvFile;
    if (file) payload.cv = file;
    submitting.value = true;
    router.put(DeveloperController.update.url(props.developer.id), payload, {
        forceFormData: !!file,
        preserveScroll: true,
        onSuccess: () => formRef.value?.clearCv?.(),
        onFinish: () => { submitting.value = false; },
    });
}
</script>

<template>
    <Head :title="`Edit ${developer.name}`" />

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
                        :title="`Edit ${developer.name}`"
                        description="Update developer information"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <Card class="lg:col-span-2">
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
                                {{ submitting ? 'Saving...' : 'Update Developer' }}
                            </Button>
                            <Button variant="outline" as-child>
                                <a :href="developersIndex().url">Cancel</a>
                            </Button>
                            <Transition
                                enter-active-class="transition ease-out duration-200"
                                enter-from-class="opacity-0 translate-y-1"
                                leave-active-class="transition ease-in duration-150"
                                leave-to-class="opacity-0"
                            >
                                <span
                                    v-show="flashSuccess"
                                    class="inline-flex items-center gap-1.5 rounded-md bg-green-500/10 px-2.5 py-1 text-sm font-medium text-green-700 dark:text-green-400"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500" />
                                    {{ flashSuccess }}
                                </span>
                            </Transition>
                        </div>
                    </form>
                </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
