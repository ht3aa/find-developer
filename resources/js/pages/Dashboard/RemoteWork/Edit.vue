<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import remoteWorkDashboard from '@/routes/dashboard/remote-work';
import type { BreadcrumbItem } from '@/types';
import { Textarea } from '@/components/ui/textarea';

type Opt = { id: number; name: string; slug: string };
type EnumOpt = { value: string; label: string };

type JobPayload = {
    id: number;
    title: string;
    slug: string;
    description: string;
    company_name: string;
    email: string;
    contact_link: string | null;
    location: string | null;
    job_title_id: number | null;
    salary_from: number | null;
    salary_to: number | null;
    salary_currency: string;
    requirements: string | null;
    status: string;
};

const props = defineProps<{
    job: JobPayload;
    jobTitles: Opt[];
    currencies: EnumOpt[];
    locations: EnumOpt[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Remote work', href: remoteWorkDashboard.index.url() },
    { title: 'Edit', href: remoteWorkDashboard.edit.url(props.job.slug) },
];

const form = useForm({
    title: props.job.title,
    description: props.job.description,
    company_name: props.job.company_name,
    email: props.job.email,
    contact_link: props.job.contact_link ?? '',
    location: props.job.location,
    job_title_id: props.job.job_title_id != null ? String(props.job.job_title_id) : '',
    salary_from: props.job.salary_from,
    salary_to: props.job.salary_to,
    salary_currency: props.job.salary_currency,
    requirements: props.job.requirements ?? '',
});

function submit(): void {
    form
        .transform((data) => ({
            ...data,
            job_title_id:
                data.job_title_id === '' || data.job_title_id === null
                    ? null
                    : Number(data.job_title_id),
            salary_from: data.salary_from === null || data.salary_from === ('' as unknown as number)
                ? null
                : Number(data.salary_from),
            salary_to: data.salary_to === null || data.salary_to === ('' as unknown as number)
                ? null
                : Number(data.salary_to),
            contact_link: data.contact_link === '' ? null : data.contact_link,
            location: data.location === '' || data.location === null ? null : data.location,
            requirements: data.requirements === '' ? null : data.requirements,
        }))
        .put(remoteWorkDashboard.update.url(props.job.slug), {
            preserveScroll: true,
        });
}
</script>

<template>
    <Head title="Edit remote work post" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full justify-center p-4">
            <Card class="w-full min-w-0 md:w-max md:max-w-full">
                <CardHeader>
                    <CardTitle>Edit remote work post</CardTitle>
                    <p class="text-sm text-muted-foreground">
                        Status: {{ job.status }}. Only pending posts can be edited from here.
                    </p>
                </CardHeader>
                <CardContent>
                    <form
                        class="grid grid-cols-1 gap-4 gap-x-8 md:grid-cols-2"
                        @submit.prevent="submit"
                    >
                        <div>
                            <Label for="title">Title</Label>
                            <Input id="title" v-model="form.title" class="mt-1" required />
                            <InputError class="mt-1" :message="form.errors.title" />
                        </div>
                        <div>
                            <Label for="company_name">Company name</Label>
                            <Input id="company_name" v-model="form.company_name" class="mt-1" required />
                            <InputError class="mt-1" :message="form.errors.company_name" />
                        </div>
                        <div>
                            <Label for="email">Contact email</Label>
                            <Input id="email" v-model="form.email" type="email" class="mt-1" required />
                            <InputError class="mt-1" :message="form.errors.email" />
                        </div>
                        <div>
                            <Label for="job_title_id">Role / job title (optional)</Label>
                            <select
                                id="job_title_id"
                                v-model="form.job_title_id"
                                class="mt-1 flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            >
                                <option value="">—</option>
                                <option
                                    v-for="jt in jobTitles"
                                    :key="jt.id"
                                    :value="String(jt.id)"
                                >
                                    {{ jt.name }}
                                </option>
                            </select>
                            <InputError class="mt-1" :message="form.errors.job_title_id" />
                        </div>
                        <div class="md:col-span-2">
                            <Label for="description">Description</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1 min-h-[140px]"
                                required
                            />
                            <InputError class="mt-1" :message="form.errors.description" />
                        </div>
                        <div class="md:col-span-2">
                            <Label for="requirements">Requirements (optional)</Label>
                            <Textarea
                                id="requirements"
                                v-model="form.requirements"
                                class="mt-1 min-h-[80px]"
                            />
                            <InputError class="mt-1" :message="form.errors.requirements" />
                        </div>
                        <div>
                            <Label for="salary_from">Salary from</Label>
                            <Input
                                id="salary_from"
                                v-model.number="form.salary_from"
                                type="number"
                                min="0"
                                class="mt-1"
                            />
                            <InputError class="mt-1" :message="form.errors.salary_from" />
                        </div>
                        <div>
                            <Label for="salary_to">Salary to</Label>
                            <Input
                                id="salary_to"
                                v-model.number="form.salary_to"
                                type="number"
                                min="0"
                                class="mt-1"
                            />
                            <InputError class="mt-1" :message="form.errors.salary_to" />
                        </div>
                        <div>
                            <Label for="salary_currency">Currency</Label>
                            <select
                                id="salary_currency"
                                v-model="form.salary_currency"
                                class="mt-1 flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            >
                                <option
                                    v-for="c in currencies"
                                    :key="c.value"
                                    :value="c.value"
                                >
                                    {{ c.label }}
                                </option>
                            </select>
                            <InputError class="mt-1" :message="form.errors.salary_currency" />
                        </div>
                        <div>
                            <Label for="location">Location (optional)</Label>
                            <select
                                id="location"
                                v-model="form.location"
                                class="mt-1 flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            >
                                <option :value="null">—</option>
                                <option
                                    v-for="loc in locations"
                                    :key="loc.value"
                                    :value="loc.value"
                                >
                                    {{ loc.label }}
                                </option>
                            </select>
                            <InputError class="mt-1" :message="form.errors.location" />
                        </div>
                        <div class="md:col-span-2">
                            <Label for="contact_link">Contact link (optional)</Label>
                            <Input id="contact_link" v-model="form.contact_link" type="url" class="mt-1" />
                            <InputError class="mt-1" :message="form.errors.contact_link" />
                        </div>
                        <div class="flex flex-wrap gap-2 md:col-span-2">
                            <Button type="submit" :disabled="form.processing">
                                Save
                            </Button>
                            <Button type="button" variant="outline" as-child>
                                <Link :href="remoteWorkDashboard.index.url()">Cancel</Link>
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
