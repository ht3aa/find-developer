<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Briefcase } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import WorkExperienceController from '@/actions/App/Http/Controllers/Dashboard/WorkExperienceController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import SearchableSelect from '@/components/SearchableSelect.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    index as workExperienceIndex,
    edit as workExperienceEdit,
} from '@/routes/work-experience';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

type JobTitleOption = { id: number; name: string };

type ParentOption = { id: number; label: string };

type WorkExperienceEdit = {
    id: number;
    company_name: string;
    job_title_id: number | null;
    job_title: string | null;
    parent_id: number | null;
    parent: { id: number; company_name: string; job_title: string | null } | null;
    description: string | null;
    start_date: string;
    end_date: string | null;
    is_current: boolean;
    show_company: boolean;
};

type Props = {
    workExperience: WorkExperienceEdit;
    jobTitles: JobTitleOption[];
    parentOptions: ParentOption[];
};

const props = defineProps<Props>();

const formData = ref({
    company_name: '',
    job_title_id: '',
    parent_id: '',
    description: '',
    start_date: '',
    end_date: '',
    is_current: false,
    show_company: true,
});

const submitting = ref(false);
const jobTitleSelectOpen = ref(false);
const parentSelectOpen = ref(false);
const page = usePage();
const formErrors = computed(() => (page.props.errors as Record<string, string>) ?? {});
const flashSuccess = computed(() => (page.props.flash as { success?: string })?.success);

function onJobTitleOpenChange(open: boolean): void {
    jobTitleSelectOpen.value = open;
    if (open) parentSelectOpen.value = false;
}

function onParentOpenChange(open: boolean): void {
    parentSelectOpen.value = open;
    if (open) jobTitleSelectOpen.value = false;
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Work Experience', href: workExperienceIndex().url },
    {
        title: props.workExperience.company_name,
        href: workExperienceEdit(props.workExperience.id).url,
    },
];

watch(
    () => props.workExperience,
    (we) => {
        formData.value = {
            company_name: we.company_name,
            job_title_id: we.job_title_id?.toString() ?? '',
            parent_id: we.parent_id?.toString() ?? '',
            description: we.description ?? '',
            start_date: we.start_date,
            end_date: we.end_date ?? '',
            is_current: we.is_current,
            show_company: we.show_company,
        };
    },
    { immediate: true },
);

function submit(): void {
    const d = formData.value;
    const payload = {
        company_name: d.company_name,
        job_title_id: d.job_title_id === '' ? '' : d.job_title_id,
        parent_id: d.parent_id === '' ? '' : d.parent_id,
        description: d.description || null,
        start_date: d.start_date,
        end_date: d.end_date || null,
        is_current: d.is_current,
        show_company: d.show_company,
    };
    submitting.value = true;
    router.put(WorkExperienceController.update.url(props.workExperience.id), payload, {
        preserveScroll: true,
        onFinish: () => {
            submitting.value = false;
        },
    });
}
</script>

<template>
    <Head :title="`Edit ${workExperience.company_name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl space-y-6 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10"
                >
                    <Briefcase class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <Heading
                        :title="`Edit ${workExperience.company_name}`"
                        description="Update your work experience details"
                    />
                </div>
            </div>

            <Card>
                <CardHeader class="pb-4">
                    <h3 class="text-sm font-medium text-muted-foreground">
                        Experience details
                    </h3>
                </CardHeader>
                <CardContent>
                    <form class="space-y-6" @submit.prevent="submit">
                        <div class="space-y-4">
                            <div class="grid gap-2">
                                <Label for="company_name">Company Name</Label>
                                <Input
                                    id="company_name"
                                    v-model="formData.company_name"
                                    required
                                    placeholder="e.g. Acme Corp"
                                    class="transition-colors focus-visible:ring-2"
                                />
                                <InputError :message="formErrors.company_name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="job_title_id_select">Job Title</Label>
                                <SearchableSelect
                                    id="job_title_id_select"
                                    v-model="formData.job_title_id"
                                    :open="jobTitleSelectOpen"
                                    :options="jobTitles.map((j) => ({ value: j.id.toString(), label: j.name }))"
                                    placeholder="Select job title (optional)"
                                    allow-clear
                                    @update:open="onJobTitleOpenChange"
                                />
                                <InputError :message="formErrors.job_title_id" />
                            </div>

                            <div
                                v-if="parentOptions.length > 0"
                                class="grid gap-2"
                            >
                                <Label for="parent_id_select">
                                    Promotion from (previous position at same
                                    company)
                                </Label>
                                <SearchableSelect
                                    id="parent_id_select"
                                    v-model="formData.parent_id"
                                    :open="parentSelectOpen"
                                    :options="parentOptions.map((p) => ({ value: p.id.toString(), label: p.label }))"
                                    placeholder="None — this is a new position"
                                    allow-clear
                                    @update:open="onParentOpenChange"
                                />
                                <p class="text-xs text-muted-foreground">
                                    Select if this role was a promotion from
                                    another position at the same company
                                </p>
                                <InputError :message="formErrors.parent_id" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="description">Description</Label>
                                <textarea
                                    id="description"
                                    v-model="formData.description"
                                    rows="4"
                                    placeholder="Describe your role and responsibilities..."
                                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                />
                                <InputError :message="formErrors.description" />
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="grid gap-2">
                                    <Label for="start_date">Start Date</Label>
                                    <Input
                                        id="start_date"
                                        v-model="formData.start_date"
                                        type="date"
                                        required
                                        class="transition-colors focus-visible:ring-2"
                                    />
                                    <InputError :message="formErrors.start_date" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="end_date">End Date</Label>
                                    <Input
                                        id="end_date"
                                        v-model="formData.end_date"
                                        type="date"
                                        class="transition-colors focus-visible:ring-2"
                                    />
                                    <InputError :message="formErrors.end_date" />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between rounded-lg border p-4">
                                    <div class="space-y-0.5">
                                        <Label for="is_current" class="text-base">
                                            I currently work here
                                        </Label>
                                        <p class="text-sm text-muted-foreground">
                                            Leave end date empty when checked
                                        </p>
                                    </div>
                                    <Checkbox
                                        id="is_current"
                                        v-model:checked="formData.is_current"
                                        class="h-5 w-5"
                                    />
                                </div>
                                <InputError :message="formErrors.is_current" />
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between rounded-lg border p-4">
                                    <div class="space-y-0.5">
                                        <Label for="show_company" class="text-base">
                                            Show on profile
                                        </Label>
                                        <p class="text-sm text-muted-foreground">
                                            Display this experience on your
                                            public profile
                                        </p>
                                    </div>
                                    <Checkbox
                                        id="show_company"
                                        v-model:checked="formData.show_company"
                                        class="h-5 w-5"
                                    />
                                </div>
                                <InputError :message="formErrors.show_company" />
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 pt-4">
                            <Button type="submit" :disabled="submitting">
                                {{ submitting ? 'Updating...' : 'Update Experience' }}
                            </Button>
                            <Button variant="outline" as-child>
                                <Link :href="workExperienceIndex().url">
                                    Cancel
                                </Link>
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
        </div>
    </AppLayout>
</template>
