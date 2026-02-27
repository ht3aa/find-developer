<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { Download, User } from 'lucide-vue-next';
import { computed, reactive, ref, watch } from 'vue';
import DeveloperProfileController from '@/actions/App/Http/Controllers/Dashboard/DeveloperProfileController';
import DeveloperCard from '@/components/DeveloperCard.vue';
import FileUpload from '@/components/FileUpload.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import SearchableSelect from '@/components/SearchableSelect.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as developerProfileIndex } from '@/routes/dashboard/developer-profile';
import { dashboard } from '@/routes';
import type { Developer } from '@/types/developer';
import type { BreadcrumbItem } from '@/types';

const availabilityTypeOptions = [
    { value: 'full-time', label: 'Full-time' },
    { value: 'part-time', label: 'Part-time' },
    { value: 'freelance', label: 'Freelance' },
    { value: 'hybrid', label: 'Hybrid' },
    { value: 'remote', label: 'Remote' },
    { value: 'remote-full-time', label: 'Remote Full-time' },
    { value: 'hybrid-full-time', label: 'Hybrid Full-time' },
] as const;

type JobTitleOption = { id: number; name: string };

type Props = {
    developer: Developer | null;
    jobTitles: JobTitleOption[];
};

const props = defineProps<Props>();

const page = usePage();
const flashSuccess = computed(() => (page.props.flash as { success?: string })?.success);
const formErrors = computed(() => (page.props.errors as Record<string, string>) ?? {});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Developer Profile', href: developerProfileIndex().url },
];

function extractYoutubeVideoId(url: string | null | undefined): string | null {
    if (!url) return null;
    const m = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/);
    return m ? m[1] : null;
}

const isAvailable = ref(false);

const previewDeveloper = computed(() => {
    if (!formData.value) return null;
    const d = formData.value;
    const youtubeUrl = (d as Record<string, unknown>).youtube_url as string | null | undefined;
    return {
        id: d.id,
        name: d.name,
        slug: d.slug ?? null,
        email: d.email,
        years_of_experience: Number(d.years_of_experience) || 0,
        phone: d.phone || null,
        expected_salary_from: d.expected_salary_from ? Number(d.expected_salary_from) : null,
        expected_salary_to: d.expected_salary_to ? Number(d.expected_salary_to) : null,
        currency: d.salary_currency || null,
        is_available: isAvailable.value,
        bio: d.bio || null,
        portfolio_url: d.portfolio_url || null,
        github_url: d.github_url || null,
        linkedin_url: d.linkedin_url || null,
        cv_path_url: d.cv_path_url ?? null,
        recommendations_received_count: d.recommendations_received_count ?? 0,
        recommended_by_us: d.recommended_by_us,
        youtube_video_id: extractYoutubeVideoId(youtubeUrl) ?? d.youtube_video_id ?? null,
        badges: d.badges ?? [],
        job_title: { name: d.job_title?.name ?? '' },
        location: d.location ? { label: d.location.label } : null,
        skills: d.skills ?? [],
        availability_type: d.availability_type ?? [],
        profile_url: d.slug ? `/developers/${d.slug}` : undefined,
    } satisfies Developer;
});

const formData = ref<Partial<Developer> | null>(null);

const youtubeUrl = computed({
    get: () => ((formData.value as Record<string, unknown> | null)?.youtube_url ?? '') as string,
    set: (v: string) => {
        if (formData.value) (formData.value as Record<string, unknown>).youtube_url = v || null;
    },
});

const jobTitleModel = computed({
    get: () => {
        const name = formData.value?.job_title?.name;
        return props.jobTitles.find((j) => j.name === name)?.id.toString() ?? null;
    },
    set: (v: string | null) => {
        if (!formData.value) return;
        if (!v) {
            formData.value.job_title = { name: '' };
            return;
        }
        const j = props.jobTitles.find((x) => x.id.toString() === v);
        if (j) formData.value.job_title = { name: j.name };
    },
});

const skillsModel = computed({
    get: () => (formData.value?.skills ?? []).map((s) => s.name),
    set: (v: string[] | string | null) => {
        if (!formData.value) return;
        const arr = Array.isArray(v) ? v : v ? [v] : [];
        formData.value.skills = arr.map((name) => ({ name: String(name) }));
    },
});

watch(
    () => props.developer,
    (dev) => {
        if (dev) {
            isAvailable.value = Boolean(dev.is_available);
            formData.value = reactive({
                ...dev,
                is_available: isAvailable.value,
                job_title: dev.job_title,
                location: dev.location,
                skills: [...(dev.skills ?? [])],
                badges: [...(dev.badges ?? [])],
                availability_type: [...(dev.availability_type ?? [])],
            });
        } else {
            formData.value = null;
            isAvailable.value = false;
        }
    },
    { immediate: true },
);

watch(isAvailable, (v) => {
    if (formData.value) formData.value.is_available = v;
});

const jobTitleSelectOpen = ref(false);
const skillSelectOpen = ref(false);
const availabilityTypeSelectOpen = ref(false);
const cvFile = ref<File | null>(null);
const cvUploadRef = ref<InstanceType<typeof FileUpload> | null>(null);

function onJobTitleOpenChange(open: boolean): void {
    jobTitleSelectOpen.value = open;
    if (open) {
        skillSelectOpen.value = false;
        availabilityTypeSelectOpen.value = false;
    }
}

function onSkillOpenChange(open: boolean): void {
    skillSelectOpen.value = open;
    if (open) {
        jobTitleSelectOpen.value = false;
        availabilityTypeSelectOpen.value = false;
    }
}

function onAvailabilityTypeOpenChange(open: boolean): void {
    availabilityTypeSelectOpen.value = open;
    if (open) {
        jobTitleSelectOpen.value = false;
        skillSelectOpen.value = false;
    }
}

const submitting = ref(false);

function submitForm(): void {
    if (!props.developer || !formData.value) return;
    const d = formData.value;
    const jobTitle = props.jobTitles.find((j) => j.name === d.job_title?.name);
    submitting.value = true;
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
        youtube_url: (d as Record<string, unknown>).youtube_url as string | null ?? null,
        is_available: isAvailable.value ? 1 : 0,
        availability_type: (d.availability_type ?? []).map((a) => a.value),
        skill_names: (d.skills ?? []).map((s) => s.name),
    };
    if (cvFile.value) {
        payload.cv = cvFile.value;
    }
    router.put(DeveloperProfileController.update.url(), payload, {
        preserveScroll: true,
        forceFormData: !!cvFile.value,
        onSuccess: () => {
            cvFile.value = null;
            cvUploadRef.value?.clear();
        },
        onFinish: () => {
            submitting.value = false;
        },
    });
}
</script>

<template>
    <Head title="Developer Profile" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10"
                    >
                        <User class="h-6 w-6 text-primary" />
                    </div>
                    <div>
                        <Heading
                            title="Developer Profile"
                            description="Edit developer information and preview the card"
                        />
                    </div>
                </div>
                <Button
                    v-if="developer"
                    variant="outline"
                    size="sm"
                    as-child
                >
                    <a :href="DeveloperProfileController.downloadCv.url()" download>
                        <Download class="mr-2 h-4 w-4" />
                        Download CV
                    </a>
                </Button>
            </div>

            <template v-if="developer && formData">
                <div class="grid gap-6 lg:grid-cols-2">
                    <Card>
                        <CardHeader class="pb-4">
                            <h3 class="text-sm font-medium text-muted-foreground">
                                Edit developer information
                            </h3>
                        </CardHeader>
                        <CardContent>
                            <form
                                class="space-y-6"
                                @submit.prevent="submitForm"
                            >
                                <div class="space-y-4">
                                    <div class="grid gap-2">
                                        <Label for="name">Name <span class="text-destructive">*</span></Label>
                                        <Input
                                            id="name"
                                            v-model="formData.name"
                                            name="name"
                                            required
                                            placeholder="Developer name"
                                            class="transition-colors focus-visible:ring-2"
                                        />
                                        <InputError :message="formErrors.name" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="email">Email <span class="text-destructive">*</span></Label>
                                        <Input
                                            id="email"
                                            v-model="formData.email"
                                            name="email"
                                            type="email"
                                            required
                                            placeholder="email@example.com"
                                            class="transition-colors focus-visible:ring-2"
                                        />
                                        <InputError :message="formErrors.email" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="phone">Phone</Label>
                                        <Input
                                            id="phone"
                                            v-model="formData.phone"
                                            name="phone"
                                            placeholder="+964..."
                                            class="transition-colors focus-visible:ring-2"
                                        />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="job_title">Job title <span class="text-destructive">*</span></Label>
                                        <SearchableSelect
                                            id="job_title"
                                            v-model="jobTitleModel"
                                            :open="jobTitleSelectOpen"
                                            :options="jobTitles.map((j: JobTitleOption) => ({ value: j.id.toString(), label: j.name }))"
                                            placeholder="e.g. Backend Developer"
                                            @update:open="onJobTitleOpenChange"
                                        />
                                        <InputError :message="formErrors.job_title_id" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="years_of_experience">Years of experience <span class="text-destructive">*</span></Label>
                                        <Input
                                            id="years_of_experience"
                                            v-model.number="formData.years_of_experience"
                                            name="years_of_experience"
                                            type="number"
                                            min="0"
                                            max="100"
                                            required
                                            class="transition-colors focus-visible:ring-2"
                                        />
                                        <InputError :message="formErrors.years_of_experience" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="bio">Bio</Label>
                                        <textarea
                                            id="bio"
                                            v-model="formData.bio"
                                            name="bio"
                                            rows="3"
                                            class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                            placeholder="Brief bio..."
                                        />
                                    </div>

                                    <Separator />

                                    <h4 class="text-sm font-medium">URLs</h4>
                                    <div class="grid gap-2 sm:grid-cols-2">
                                        <div class="grid gap-2">
                                            <Label for="portfolio_url">Portfolio</Label>
                                            <Input
                                                id="portfolio_url"
                                                v-model="formData.portfolio_url"
                                                name="portfolio_url"
                                                type="url"
                                                placeholder="https://..."
                                                class="transition-colors focus-visible:ring-2"
                                            />
                                        </div>
                                        <div class="grid gap-2">
                                            <Label for="github_url">GitHub</Label>
                                            <Input
                                                id="github_url"
                                                v-model="formData.github_url"
                                                name="github_url"
                                                type="url"
                                                placeholder="https://github.com/..."
                                                class="transition-colors focus-visible:ring-2"
                                            />
                                        </div>
                                        <div class="grid gap-2">
                                            <Label for="linkedin_url">LinkedIn</Label>
                                            <Input
                                                id="linkedin_url"
                                                v-model="formData.linkedin_url"
                                                name="linkedin_url"
                                                type="url"
                                                placeholder="https://linkedin.com/..."
                                                class="transition-colors focus-visible:ring-2"
                                            />
                                        </div>
                                        <div class="grid gap-2">
                                            <Label for="youtube_url">YouTube</Label>
                                            <Input
                                                id="youtube_url"
                                                v-model="youtubeUrl"
                                                name="youtube_url"
                                                type="url"
                                                placeholder="https://youtube.com/..."
                                                class="transition-colors focus-visible:ring-2"
                                            />
                                        </div>
                                    </div>

                                    <FileUpload
                                        id="cv"
                                        ref="cvUploadRef"
                                        v-model="cvFile"
                                        label="CV (PDF, max 10MB)"
                                        accept=".pdf,application/pdf"
                                        :existing-url="formData?.cv_path_url ?? null"
                                        existing-label="View current CV"
                                        :error="formErrors.cv"
                                    />

                                    <Separator />

                                    <div class="grid gap-2">
                                        <Label for="skill_ids">Skills</Label>
                                        <SearchableSelect
                                            id="skill_ids"
                                            v-model="skillsModel"
                                            :open="skillSelectOpen"
                                            options-url="/api/skills"
                                            placeholder="e.g. Laravel, Vue"
                                            multiple
                                            :max-options="50"
                                            @update:open="onSkillOpenChange"
                                        />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="availability_type">Availability type</Label>
                                        <SearchableSelect
                                            id="availability_type"
                                            :model-value="(formData.availability_type ?? []).map((a: { value: string }) => a.value)"
                                            :open="availabilityTypeSelectOpen"
                                            :options="availabilityTypeOptions"
                                            placeholder="e.g. Full-time, Remote"
                                            multiple
                                            @update:model-value="
                                                (v) => {
                                                    const arr = Array.isArray(v) ? v : v ? [v] : [];
                                                    if (formData) {
                                                        formData.availability_type = arr.map((val) => {
                                                            const opt = availabilityTypeOptions.find((o) => o.value === val);
                                                            return { value: String(val), label: opt?.label ?? String(val) };
                                                        });
                                                    }
                                                }
                                            "
                                            @update:open="onAvailabilityTypeOpenChange"
                                        />
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center space-x-2">
                                            <input
                                                id="is_available"
                                                v-model="isAvailable"
                                                type="checkbox"
                                                name="is_available"
                                                value="1"
                                                class="size-4 shrink-0 rounded border border-input shadow-xs outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50"
                                            />
                                            <Label for="is_available">Available</Label>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-3 pt-4">
                                    <Button type="submit" :disabled="submitting">
                                        {{ submitting ? 'Saving...' : 'Save changes' }}
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

                    <Card class="h-fit">
                        <CardHeader class="pb-4">
                            <h3 class="text-sm font-medium text-muted-foreground">
                                Card preview
                            </h3>
                            <p class="text-xs text-muted-foreground">
                                Changes are reflected in real-time
                            </p>
                        </CardHeader>
                        <CardContent>
                            <DeveloperCard
                                v-if="previewDeveloper"
                                :developer="previewDeveloper"
                            />
                        </CardContent>
                    </Card>
                </div>
            </template>

            <div
                v-else
                class="flex flex-col items-center justify-center rounded-xl border border-dashed py-12"
            >
                <User class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No developer profile</h3>
                <p class="text-center text-sm text-muted-foreground">
                    You do not have a developer profile. Please contact an administrator to set one up.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
