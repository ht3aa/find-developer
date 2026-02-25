<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import DeveloperFormFields from '@/components/developers/DeveloperFormFields.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login, register } from '@/routes';
import { computed, ref } from 'vue';

type JobTitleOption = { id: number; name: string };

const props = withDefaults(
    defineProps<{
        jobTitles?: JobTitleOption[];
    }>(),
    { jobTitles: () => [] },
);

const formData = ref<Record<string, unknown>>({
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

function submit(): void {
    const d = formData.value;
    const jobTitle = props.jobTitles.find(
        (j) => j.name === (d.job_title as { name?: string })?.name,
    );
    const payload: Record<string, unknown> = {
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
    };
    const cvFile = formRef.value?.cvFile;
    const file = typeof cvFile === 'object' && cvFile && 'value' in cvFile ? cvFile.value : cvFile;
    if (file) payload.cv = file;
    submitting.value = true;
    router.post(register(), payload as Record<string, string | number | boolean | null | string[] | File | Blob>, {
        forceFormData: !!file,
        preserveScroll: true,
        onSuccess: () => formRef.value?.clearCv?.(),
        onError: () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        onFinish: () => { submitting.value = false; },
    });
}
</script>

<template>
    <AuthBase
        title="Register as a developer"
        description="Enter your details and developer profile below"
    >
        <Head title="Register as a developer" />

        <form class="flex flex-col gap-6" @submit.prevent="submit">
            <DeveloperFormFields
                ref="formRef"
                v-model="formData"
                :errors="formErrors"
                :job-titles="jobTitles"
            />

            <Button
                type="submit"
                class="w-full"
                :disabled="submitting"
                data-test="register-user-button"
            >
                <Spinner v-if="submitting" />
                Create account
            </Button>

            <Separator class="my-2" />

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="login()">
                    Log in
                </TextLink>
            </div>
        </form>
    </AuthBase>
</template>
