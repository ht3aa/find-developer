<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { Check, Circle, Download, Info, User } from 'lucide-vue-next';
import { computed, reactive, ref, watch } from 'vue';
import DeveloperProfileController from '@/actions/App/Http/Controllers/Dashboard/DeveloperProfileController';
import DeveloperCard from '@/components/DeveloperCard.vue';
import FileUpload from '@/components/FileUpload.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import SearchableSelect from '@/components/SearchableSelect.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as developerProfileIndex } from '@/routes/dashboard/developer-profile';
import type { BreadcrumbItem } from '@/types';
import type { Developer } from '@/types/developer';
import {
    formatSalaryDisplay,
    parseSalaryDigitsInput,
    parseSalaryForForm,
    SALARY_CURRENCY_OPTIONS,
} from '@/utils/salary';

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
const flashSuccess = computed(
    () => (page.props.flash as { success?: string })?.success,
);
const formErrors = computed(
    () => (page.props.errors as Record<string, string>) ?? {},
);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Developer Profile', href: developerProfileIndex().url },
];

function extractYoutubeVideoId(url: string | null | undefined): string | null {
    if (!url) return null;
    const m = url.match(
        /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/,
    );
    return m ? m[1] : null;
}

const isAvailable = ref(false);

const previewDeveloper = computed(() => {
    if (!formData.value) return null;
    const d = formData.value;
    const youtubeUrl = (d as Record<string, unknown>).youtube_url as
        | string
        | null
        | undefined;
    return {
        id: d.id,
        name: d.name,
        slug: d.slug ?? null,
        email: d.email,
        years_of_experience: Number(d.years_of_experience) || 0,
        phone: d.phone || null,
        expected_salary_from: parseSalaryForForm(d.expected_salary_from),
        expected_salary_to: parseSalaryForForm(d.expected_salary_to),
        currency:
            (d as Record<string, unknown>).salary_currency?.toString() ??
            d.currency ??
            null,
        is_available: isAvailable.value,
        bio: d.bio || null,
        portfolio_url: d.portfolio_url || null,
        github_url: d.github_url || null,
        linkedin_url: d.linkedin_url || null,
        cv_path_url: d.cv_path_url ?? null,
        recommendations_received_count: d.recommendations_received_count ?? 0,
        recommended_by_us: d.recommended_by_us,
        youtube_video_id:
            extractYoutubeVideoId(youtubeUrl) ?? d.youtube_video_id ?? null,
        badges: d.badges ?? [],
        job_title: { name: d.job_title?.name ?? '' },
        location: (() => {
            const raw = (d as Record<string, unknown>).location;
            if (typeof raw === 'string' && raw !== '') {
                const saved = props.developer?.location;
                if (saved && saved.value === raw) {
                    return { value: saved.value, label: saved.label };
                }
                return { value: raw, label: raw };
            }
            if (
                raw &&
                typeof raw === 'object' &&
                'label' in (raw as object)
            ) {
                return {
                    value:
                        (raw as { value?: string }).value ??
                        (raw as { label: string }).label,
                    label: (raw as { label: string }).label,
                };
            }
            return null;
        })(),
        skills: d.skills ?? [],
        availability_type: d.availability_type ?? [],
        profile_url: d.slug ? `/developers/${d.slug}` : undefined,
    } satisfies Developer;
});

const formData = ref<Partial<Developer> | null>(null);

const youtubeUrl = computed({
    get: () =>
        ((formData.value as Record<string, unknown> | null)?.youtube_url ??
            '') as string,
    set: (v: string) => {
        if (formData.value)
            (formData.value as Record<string, unknown>).youtube_url = v || null;
    },
});

const profileLocationModel = computed({
    get: () => {
        const v = formData.value as Record<string, unknown> | null;
        if (!v) return null;
        const loc = v.location;
        return typeof loc === 'string' && loc !== '' ? loc : null;
    },
    set: (val: string | null) => {
        if (!formData.value) return;
        (formData.value as Record<string, unknown>).location =
            val && val !== '' ? val : null;
    },
});

const jobTitleModel = computed({
    get: () => {
        const name = formData.value?.job_title?.name;
        return (
            props.jobTitles.find((j) => j.name === name)?.id.toString() ?? null
        );
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

const updateCvAutomatic = ref(false);

watch(
    () => props.developer,
    (dev) => {
        if (dev) {
            isAvailable.value = Boolean(dev.is_available);
            updateCvAutomatic.value = Boolean(dev.update_cv_automatic);
            formData.value = reactive({
                ...dev,
                is_available: isAvailable.value,
                update_cv_automatic: updateCvAutomatic.value,
                job_title: dev.job_title,
                location: dev.location?.value ?? null,
                skills: [...(dev.skills ?? [])],
                badges: [...(dev.badges ?? [])],
                availability_type: [...(dev.availability_type ?? [])],
                expected_salary_from: parseSalaryForForm(
                    (dev as Record<string, unknown>).expected_salary_from,
                ),
                expected_salary_to: parseSalaryForForm(
                    (dev as Record<string, unknown>).expected_salary_to,
                ),
                salary_currency:
                    (dev as Record<string, unknown>).currency?.toString() ??
                    'IQD',
            });
        } else {
            formData.value = null;
            isAvailable.value = false;
            updateCvAutomatic.value = false;
        }
    },
    { immediate: true },
);

watch(isAvailable, (v) => {
    if (formData.value) formData.value.is_available = v;
});

watch(updateCvAutomatic, (v) => {
    if (formData.value) formData.value.update_cv_automatic = v;
});

const newsletterTodos = computed(() => {
    const d = formData.value;
    const badgesCount = (d?.badges ?? []).length;
    const hasWorkExperience =
        (props.developer?.work_experience ?? []).length > 0;
    const hasProjects = (props.developer?.projects ?? []).length > 0;
    const hasCv = Boolean(d?.cv_path_url);
    const hasSkills = (d?.skills ?? []).length > 0;
    return [
        { label: 'Marked as available', done: isAvailable.value },
        { label: 'At least 2 badges', done: badgesCount >= 2 },
        { label: 'Work experience', done: hasWorkExperience },
        { label: 'Projects', done: hasProjects },
        { label: 'CV uploaded', done: hasCv },
        { label: 'Skills added', done: hasSkills },
    ];
});

const allNewsletterRequirementsMet = computed(
    () =>
        newsletterTodos.value.length > 0 &&
        newsletterTodos.value.every((t) => t.done),
);

const jobTitleSelectOpen = ref(false);
const locationSelectOpen = ref(false);
const skillSelectOpen = ref(false);
const availabilityTypeSelectOpen = ref(false);
const cvFile = ref<File | null>(null);
const cvUploadRef = ref<InstanceType<typeof FileUpload> | null>(null);
const showExperienceBadgeModal = ref(false);

const hasExperienceValidatedBadge = computed(
    () =>
        props.developer?.badges?.some(
            (b) => b.slug === 'experience-validated',
        ) ?? false,
);

function onJobTitleOpenChange(open: boolean): void {
    jobTitleSelectOpen.value = open;
    if (open) {
        locationSelectOpen.value = false;
        skillSelectOpen.value = false;
        availabilityTypeSelectOpen.value = false;
    }
}

function onLocationOpenChange(open: boolean): void {
    locationSelectOpen.value = open;
    if (open) {
        jobTitleSelectOpen.value = false;
        skillSelectOpen.value = false;
        availabilityTypeSelectOpen.value = false;
    }
}

function onSkillOpenChange(open: boolean): void {
    skillSelectOpen.value = open;
    if (open) {
        jobTitleSelectOpen.value = false;
        locationSelectOpen.value = false;
        availabilityTypeSelectOpen.value = false;
    }
}

function onAvailabilityTypeOpenChange(open: boolean): void {
    availabilityTypeSelectOpen.value = open;
    if (open) {
        jobTitleSelectOpen.value = false;
        locationSelectOpen.value = false;
        skillSelectOpen.value = false;
    }
}

const submitting = ref(false);

function buildPayload(): Record<string, unknown> | null {
    if (!props.developer || !formData.value) return null;
    const d = formData.value;
    const jobTitle = props.jobTitles.find((j) => j.name === d.job_title?.name);
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
        youtube_url:
            ((d as Record<string, unknown>).youtube_url as string | null) ??
            null,
        is_available: isAvailable.value ? 1 : 0,
        availability_type: (d.availability_type ?? []).map((a) => a.value),
        skill_names: (d.skills ?? []).map((s) => s.name),
        update_cv_automatic: updateCvAutomatic.value ? 1 : 0,
        expected_salary_from: parseSalaryForForm(
            (d as Record<string, unknown>).expected_salary_from as
                | string
                | number
                | null
                | undefined,
        ),
        expected_salary_to: parseSalaryForForm(
            (d as Record<string, unknown>).expected_salary_to as
                | string
                | number
                | null
                | undefined,
        ),
        salary_currency:
            ((d as Record<string, unknown>).salary_currency as string) ??
            'IQD',
        location:
            typeof (d as Record<string, unknown>).location === 'string' &&
            (d as Record<string, string>).location !== ''
                ? (d as Record<string, string>).location
                : null,
    };
    if (cvFile.value) {
        payload.cv = cvFile.value;
    }
    return payload;
}

function doSubmit(): void {
    const payload = buildPayload();
    if (!payload) return;
    submitting.value = true;
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

function submitForm(): void {
    const payload = buildPayload();
    if (!payload) return;
    const experienceChanged =
        Number(formData.value?.years_of_experience) !==
        Number(props.developer?.years_of_experience);
    if (hasExperienceValidatedBadge.value && experienceChanged) {
        showExperienceBadgeModal.value = true;
        return;
    }
    doSubmit();
}

function confirmExperienceChangeAndSubmit(): void {
    showExperienceBadgeModal.value = false;
    doSubmit();
}

function setProfileSalaryFrom(v: unknown): void {
    if (!formData.value) {
        return;
    }
    const d = formData.value as Record<string, unknown>;
    if (v === '' || v === null || v === undefined) {
        d.expected_salary_from = null;
    } else {
        d.expected_salary_from = parseSalaryDigitsInput(String(v));
    }
}

function setProfileSalaryTo(v: unknown): void {
    if (!formData.value) {
        return;
    }
    const d = formData.value as Record<string, unknown>;
    if (v === '' || v === null || v === undefined) {
        d.expected_salary_to = null;
    } else {
        d.expected_salary_to = parseSalaryDigitsInput(String(v));
    }
}
</script>

<template>
    <Head title="Developer Profile" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
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
                <Button v-if="developer" variant="outline" size="sm" as-child>
                    <a
                        :href="DeveloperProfileController.downloadCv.url()"
                        download
                    >
                        <Download class="mr-2 h-4 w-4" />
                        Download CV
                    </a>
                </Button>
            </div>

            <template v-if="developer && formData">
                <div
                    class="rounded-lg border border-amber-500/30 bg-amber-500/10 p-4"
                >
                    <p class="flex gap-2 text-sm text-muted-foreground">
                        <Info
                            class="mt-0.5 h-4 w-4 shrink-0 text-amber-600 dark:text-amber-400"
                        />
                        <span>
                            We will be marketing your profile to companies and
                            clients. Please fill in your profile correctly with
                            all data and keep it up to date.
                        </span>
                    </p>
                </div>

                <template v-if="allNewsletterRequirementsMet">
                    <div
                        class="rounded-lg border border-green-500/30 bg-green-500/10 p-4"
                    >
                        <p
                            class="flex gap-2 text-sm text-green-700 dark:text-green-400"
                        >
                            <Check class="mt-0.5 h-4 w-4 shrink-0" />
                            <span>
                                Your profile will be included in the newsletter
                                sent to companies emails.
                            </span>
                        </p>
                    </div>
                </template>
                <template v-else>
                    <div
                        class="rounded-lg border border-primary/20 bg-primary/5 p-4"
                    >
                        <p
                            class="mb-3 flex gap-2 text-sm text-muted-foreground"
                        >
                            <Info
                                class="mt-0.5 h-4 w-4 shrink-0 text-primary"
                            />
                            <span>
                                Only developers who are available, have at least
                                2 badges, work experience, projects, a CV, and
                                skills are included in the newsletter sent to
                                companies emails.
                            </span>
                        </p>
                        <ul class="flex flex-wrap gap-x-6 gap-y-1.5 text-sm">
                            <li
                                v-for="todo in newsletterTodos"
                                :key="todo.label"
                                class="flex items-center gap-2"
                            >
                                <Check
                                    v-if="todo.done"
                                    class="h-4 w-4 shrink-0 text-green-600 dark:text-green-400"
                                />
                                <Circle
                                    v-else
                                    class="h-4 w-4 shrink-0 text-muted-foreground"
                                />
                                <span
                                    :class="{
                                        'text-muted-foreground': !todo.done,
                                    }"
                                >
                                    {{ todo.label }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </template>

                <Dialog
                    :open="showExperienceBadgeModal"
                    @update:open="showExperienceBadgeModal = $event"
                >
                    <DialogContent
                        :show-close-button="true"
                        class="sm:max-w-md"
                    >
                        <DialogHeader>
                            <DialogTitle>Experience changed</DialogTitle>
                            <DialogDescription>
                                Changing your years of experience will remove
                                your
                                <strong>Experience Validated</strong> badge. You
                                will need to re-schedule a meeting with an admin
                                to get the badge again. Do you want to continue?
                            </DialogDescription>
                        </DialogHeader>
                        <DialogFooter class="gap-2 sm:gap-0">
                            <Button
                                variant="outline"
                                @click="showExperienceBadgeModal = false"
                            >
                                Cancel
                            </Button>
                            <Button @click="confirmExperienceChangeAndSubmit">
                                Continue
                            </Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>

                <div class="grid items-start gap-6 lg:grid-cols-2">
                    <Card>
                        <CardHeader class="pb-4">
                            <h3
                                class="text-sm font-medium text-muted-foreground"
                            >
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
                                        <Label for="name"
                                            >Name
                                            <span class="text-destructive"
                                                >*</span
                                            ></Label
                                        >
                                        <Input
                                            id="name"
                                            v-model="formData.name"
                                            name="name"
                                            required
                                            placeholder="Developer name"
                                            class="transition-colors focus-visible:ring-2"
                                        />
                                        <InputError
                                            :message="formErrors.name"
                                        />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="email"
                                            >Email
                                            <span class="text-destructive"
                                                >*</span
                                            ></Label
                                        >
                                        <Input
                                            id="email"
                                            v-model="formData.email"
                                            name="email"
                                            type="email"
                                            required
                                            placeholder="email@example.com"
                                            class="transition-colors focus-visible:ring-2"
                                        />
                                        <InputError
                                            :message="formErrors.email"
                                        />
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
                                        <p
                                            class="text-xs text-muted-foreground"
                                        >
                                            Hidden from public view — only
                                            visible to recruiters.
                                        </p>
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="location"
                                            >Location (optional)</Label
                                        >
                                        <SearchableSelect
                                            id="location"
                                            v-model="profileLocationModel"
                                            :open="locationSelectOpen"
                                            options-url="/api/locations"
                                            placeholder="Search governorate..."
                                            :allow-clear="true"
                                            @update:open="onLocationOpenChange"
                                        />
                                        <InputError
                                            :message="formErrors.location"
                                        />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="job_title"
                                            >Job title
                                            <span class="text-destructive"
                                                >*</span
                                            ></Label
                                        >
                                        <SearchableSelect
                                            id="job_title"
                                            v-model="jobTitleModel"
                                            :open="jobTitleSelectOpen"
                                            :options="
                                                jobTitles.map(
                                                    (j: JobTitleOption) => ({
                                                        value: j.id.toString(),
                                                        label: j.name,
                                                    }),
                                                )
                                            "
                                            placeholder="e.g. Backend Developer"
                                            @update:open="onJobTitleOpenChange"
                                        />
                                        <InputError
                                            :message="formErrors.job_title_id"
                                        />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="years_of_experience"
                                            >Years of experience
                                            <span class="text-destructive"
                                                >*</span
                                            ></Label
                                        >
                                        <Input
                                            id="years_of_experience"
                                            v-model.number="
                                                formData.years_of_experience
                                            "
                                            name="years_of_experience"
                                            type="number"
                                            min="0"
                                            max="100"
                                            required
                                            class="transition-colors focus-visible:ring-2"
                                        />
                                        <InputError
                                            :message="
                                                formErrors.years_of_experience
                                            "
                                        />
                                    </div>

                                    <div class="space-y-4">
                                        <div class="grid gap-2">
                                            <Label>Expected salary (optional)</Label>
                                            <p
                                                class="text-xs text-muted-foreground"
                                            >
                                                Hidden from public view — only
                                                visible to recruiters with CV
                                                access.
                                            </p>
                                        </div>
                                        <div
                                            class="grid gap-4 sm:grid-cols-2"
                                        >
                                            <div class="grid gap-2">
                                                <Label for="expected_salary_from"
                                                    >From</Label
                                                >
                                                <Input
                                                    id="expected_salary_from"
                                                    type="text"
                                                    inputmode="numeric"
                                                    autocomplete="off"
                                                    name="expected_salary_from"
                                                    placeholder="e.g. 1,500,000"
                                                    class="transition-colors focus-visible:ring-2"
                                                    :model-value="
                                                        formatSalaryDisplay(
                                                            formData.expected_salary_from,
                                                        )
                                                    "
                                                    @update:model-value="
                                                        setProfileSalaryFrom
                                                    "
                                                />
                                                <InputError
                                                    :message="
                                                        formErrors.expected_salary_from
                                                    "
                                                />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="expected_salary_to"
                                                    >To</Label
                                                >
                                                <Input
                                                    id="expected_salary_to"
                                                    type="text"
                                                    inputmode="numeric"
                                                    autocomplete="off"
                                                    name="expected_salary_to"
                                                    placeholder="e.g. 2,000,000"
                                                    class="transition-colors focus-visible:ring-2"
                                                    :model-value="
                                                        formatSalaryDisplay(
                                                            formData.expected_salary_to,
                                                        )
                                                    "
                                                    @update:model-value="
                                                        setProfileSalaryTo
                                                    "
                                                />
                                                <InputError
                                                    :message="
                                                        formErrors.expected_salary_to
                                                    "
                                                />
                                            </div>
                                            <div class="grid gap-2 sm:col-span-2">
                                                <Label for="salary_currency"
                                                    >Currency</Label
                                                >
                                                <select
                                                    id="salary_currency"
                                                    v-model="
                                                        formData.salary_currency
                                                    "
                                                    name="salary_currency"
                                                    class="border-input bg-background ring-offset-background focus-visible:ring-ring flex h-9 w-full rounded-md border px-3 py-1 text-sm shadow-sm transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                                >
                                                    <option
                                                        v-for="opt in SALARY_CURRENCY_OPTIONS"
                                                        :key="opt.value"
                                                        :value="opt.value"
                                                    >
                                                        {{ opt.label }}
                                                    </option>
                                                </select>
                                                <InputError
                                                    :message="
                                                        formErrors.salary_currency
                                                    "
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="bio">Bio</Label>
                                        <textarea
                                            id="bio"
                                            v-model="formData.bio"
                                            name="bio"
                                            rows="3"
                                            class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none"
                                            placeholder="Brief bio..."
                                        />
                                    </div>

                                    <Separator />

                                    <h4 class="text-sm font-medium">URLs</h4>
                                    <div class="grid gap-2 sm:grid-cols-2">
                                        <div class="grid gap-2">
                                            <Label for="portfolio_url"
                                                >Portfolio</Label
                                            >
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
                                            <Label for="github_url"
                                                >GitHub</Label
                                            >
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
                                            <Label for="linkedin_url"
                                                >LinkedIn</Label
                                            >
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
                                            <Label for="youtube_url"
                                                >YouTube</Label
                                            >
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

                                    <div class="grid gap-2">
                                        <FileUpload
                                            id="cv"
                                            ref="cvUploadRef"
                                            v-model="cvFile"
                                            label="CV (PDF, max 10MB)"
                                            accept=".pdf,application/pdf"
                                            :existing-url="
                                                formData?.cv_path_url ?? null
                                            "
                                            existing-label="View current CV"
                                            :error="formErrors.cv"
                                        />
                                        <p
                                            class="text-xs text-muted-foreground"
                                        >
                                            Hidden from public view — only
                                            visible to recruiters.
                                        </p>
                                    </div>

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
                                        <Label for="availability_type"
                                            >Availability type</Label
                                        >
                                        <SearchableSelect
                                            id="availability_type"
                                            :model-value="
                                                (
                                                    formData.availability_type ??
                                                    []
                                                ).map(
                                                    (a: { value: string }) =>
                                                        a.value,
                                                )
                                            "
                                            :open="availabilityTypeSelectOpen"
                                            :options="availabilityTypeOptions"
                                            placeholder="e.g. Full-time, Remote"
                                            multiple
                                            @update:model-value="
                                                (v) => {
                                                    const arr = Array.isArray(v)
                                                        ? v
                                                        : v
                                                          ? [v]
                                                          : [];
                                                    if (formData) {
                                                        formData.availability_type =
                                                            arr.map((val) => {
                                                                const opt =
                                                                    availabilityTypeOptions.find(
                                                                        (o) =>
                                                                            o.value ===
                                                                            val,
                                                                    );
                                                                return {
                                                                    value: String(
                                                                        val,
                                                                    ),
                                                                    label:
                                                                        opt?.label ??
                                                                        String(
                                                                            val,
                                                                        ),
                                                                };
                                                            });
                                                    }
                                                }
                                            "
                                            @update:open="
                                                onAvailabilityTypeOpenChange
                                            "
                                        />
                                    </div>

                                    <div
                                        class="flex flex-wrap items-center gap-4"
                                    >
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <input
                                                id="is_available"
                                                v-model="isAvailable"
                                                type="checkbox"
                                                name="is_available"
                                                value="1"
                                                class="size-4 shrink-0 rounded border border-input shadow-xs outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50"
                                            />
                                            <Label for="is_available"
                                                >Available</Label
                                            >
                                        </div>
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <input
                                                id="update_cv_automatic"
                                                v-model="updateCvAutomatic"
                                                type="checkbox"
                                                name="update_cv_automatic"
                                                value="1"
                                                class="size-4 shrink-0 rounded border border-input shadow-xs outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50"
                                            />
                                            <Label for="update_cv_automatic"
                                                >Auto-update CV when profile,
                                                Work Experience and Projects are
                                                updated
                                            </Label>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex flex-wrap items-center gap-3 pt-4"
                                >
                                    <Button
                                        type="submit"
                                        :disabled="submitting"
                                    >
                                        {{
                                            submitting
                                                ? 'Saving...'
                                                : 'Save changes'
                                        }}
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
                                            <span
                                                class="h-1.5 w-1.5 rounded-full bg-green-500"
                                            />
                                            {{ flashSuccess }}
                                        </span>
                                    </Transition>
                                </div>
                            </form>
                        </CardContent>
                    </Card>

                    <div class="lg:sticky lg:top-4">
                        <Card>
                            <CardHeader class="pb-4">
                                <h3
                                    class="text-sm font-medium text-muted-foreground"
                                >
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
                </div>
            </template>

            <div
                v-else
                class="flex flex-col items-center justify-center rounded-xl border border-dashed py-12"
            >
                <User class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No developer profile</h3>
                <p class="text-center text-sm text-muted-foreground">
                    You do not have a developer profile. Please contact an
                    administrator to set one up.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
