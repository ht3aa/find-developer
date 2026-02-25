<script setup lang="ts">
import { computed, ref } from 'vue';
import { Form, Head, Link } from '@inertiajs/vue3';
import { Briefcase } from 'lucide-vue-next';
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

const jobTitleId = ref(
    props.workExperience.job_title_id?.toString() ?? '',
);
const parentId = ref(props.workExperience.parent_id?.toString() ?? '');

const jobTitleOptions = computed(() => [
    { value: '', label: 'Select job title (optional)' },
    ...props.jobTitles.map((j) => ({ value: j.id.toString(), label: j.name })),
]);

const parentSelectOptions = computed(() => [
    { value: '', label: 'None — this is a new position' },
    ...props.parentOptions.map((p) => ({
        value: p.id.toString(),
        label: p.label,
    })),
]);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Work Experience', href: workExperienceIndex().url },
    {
        title: props.workExperience.company_name,
        href: workExperienceEdit(props.workExperience.id).url,
    },
];
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
                <Form
                    v-bind="
                        WorkExperienceController.update.form(workExperience.id)
                    "
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <input type="hidden" name="_method" value="PUT" />
                    <CardHeader class="pb-4">
                        <h3 class="text-sm font-medium text-muted-foreground">
                            Experience details
                        </h3>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="space-y-4">
                            <div class="grid gap-2">
                                <Label for="company_name">Company Name</Label>
                                <Input
                                    id="company_name"
                                    name="company_name"
                                    :default-value="workExperience.company_name"
                                    required
                                    placeholder="e.g. Acme Corp"
                                    class="transition-colors focus-visible:ring-2"
                                />
                                <InputError :message="errors.company_name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="job_title_id">Job Title</Label>
                                <input
                                    type="hidden"
                                    name="job_title_id"
                                    :value="jobTitleId ?? ''"
                                />
                                <SearchableSelect
                                    id="job_title_id"
                                    v-model="jobTitleId"
                                    :options="jobTitleOptions"
                                    placeholder="Select job title (optional)"
                                    allow-clear
                                />
                                <InputError :message="errors.job_title_id" />
                            </div>

                            <div
                                v-if="parentOptions.length > 0"
                                class="grid gap-2"
                            >
                                <Label for="parent_id">
                                    Promotion from (previous position at same
                                    company)
                                </Label>
                                <input
                                    type="hidden"
                                    name="parent_id"
                                    :value="parentId ?? ''"
                                />
                                <SearchableSelect
                                    id="parent_id"
                                    v-model="parentId"
                                    :options="parentSelectOptions"
                                    placeholder="None — this is a new position"
                                    allow-clear
                                />
                                <p class="text-xs text-muted-foreground">
                                    Select if this role was a promotion from
                                    another position at the same company
                                </p>
                                <InputError :message="errors.parent_id" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="description">Description</Label>
                                <textarea
                                    id="description"
                                    name="description"
                                    :default-value="
                                        workExperience.description ?? ''
                                    "
                                    rows="4"
                                    placeholder="Describe your role and responsibilities..."
                                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                />
                                <InputError :message="errors.description" />
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="grid gap-2">
                                    <Label for="start_date">Start Date</Label>
                                    <Input
                                        id="start_date"
                                        name="start_date"
                                        type="date"
                                        :default-value="
                                            workExperience.start_date
                                        "
                                        required
                                        class="transition-colors focus-visible:ring-2"
                                    />
                                    <InputError :message="errors.start_date" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="end_date">End Date</Label>
                                    <Input
                                        id="end_date"
                                        name="end_date"
                                        type="date"
                                        :default-value="
                                            workExperience.end_date ?? ''
                                        "
                                        class="transition-colors focus-visible:ring-2"
                                    />
                                    <InputError :message="errors.end_date" />
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
                                    <input type="hidden" name="is_current" value="0" />
                                    <Checkbox
                                        id="is_current"
                                        name="is_current"
                                        value="1"
                                        :default-value="
                                            workExperience.is_current
                                        "
                                        class="h-5 w-5"
                                    />
                                </div>
                                <InputError :message="errors.is_current" />
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
                                    <input type="hidden" name="show_company" value="0" />
                                    <Checkbox
                                        id="show_company"
                                        name="show_company"
                                        value="1"
                                        :default-value="
                                            workExperience.show_company
                                        "
                                        class="h-5 w-5"
                                    />
                                </div>
                                <InputError :message="errors.show_company" />
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <Button :disabled="processing" type="submit">
                                Update Experience
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
                                    v-show="recentlySuccessful"
                                    class="inline-flex items-center gap-1.5 rounded-md bg-green-500/10 px-2.5 py-1 text-sm font-medium text-green-700 dark:text-green-400"
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-green-500"
                                    />
                                    Work experience updated successfully
                                </span>
                            </Transition>
                        </div>
                    </CardContent>
                </Form>
            </Card>
        </div>
    </AppLayout>
</template>
