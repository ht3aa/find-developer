<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { FolderKanban } from 'lucide-vue-next';
import DeveloperProjectController from '@/actions/App/Http/Controllers/Dashboard/DeveloperProjectController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    index as developerProjectsIndex,
    create as developerProjectsCreate,
} from '@/routes/developer-projects';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

defineProps<{}>();

const formData = ref({
    title: '',
    description: '',
    link: '',
    show_project: true,
});

const submitting = ref(false);
const page = usePage();
const formErrors = computed(() => (page.props.errors as Record<string, string>) ?? {});
const flashSuccess = computed(() => (page.props.flash as { success?: string })?.success);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Projects', href: developerProjectsIndex().url },
    { title: 'Add', href: developerProjectsCreate().url },
];

function submit(): void {
    const d = formData.value;
    const payload = {
        title: d.title,
        description: d.description || null,
        link: d.link || null,
        show_project: d.show_project,
    };
    submitting.value = true;
    router.post(DeveloperProjectController.store.url(), payload, {
        preserveScroll: true,
        onFinish: () => {
            submitting.value = false;
        },
    });
}
</script>

<template>
    <Head title="Add Project" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full space-y-6 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10"
                >
                    <FolderKanban class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <Heading
                        title="Add Project"
                        description="Add a new project to your portfolio"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <Card class="lg:col-span-2">
                    <CardHeader class="pb-4">
                        <h3 class="text-sm font-medium text-muted-foreground">
                            Project details
                        </h3>
                    </CardHeader>
                    <CardContent class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <form class="contents" @submit.prevent="submit">
                            <div class="space-y-4">
                                <div class="grid gap-2">
                                    <Label for="title">Title <span class="text-destructive">*</span></Label>
                                    <Input
                                        id="title"
                                        v-model="formData.title"
                                        required
                                        placeholder="e.g. E-commerce Platform"
                                        class="transition-colors focus-visible:ring-2"
                                    />
                                    <InputError :message="formErrors.title" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="link">Link (URL)</Label>
                                    <Input
                                        id="link"
                                        v-model="formData.link"
                                        type="url"
                                        placeholder="https://..."
                                        class="transition-colors focus-visible:ring-2"
                                    />
                                    <InputError :message="formErrors.link" />
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="grid gap-2">
                                    <Label for="description">Description</Label>
                                    <textarea
                                        id="description"
                                        v-model="formData.description"
                                        rows="4"
                                        placeholder="Describe the project, technologies used, and your role..."
                                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    />
                                    <InputError :message="formErrors.description" />
                                </div>
                            </div>

                            <div class="space-y-2 lg:col-span-2">
                                <div class="flex items-center justify-between rounded-lg border p-4">
                                    <div class="space-y-0.5">
                                        <Label for="show_project" class="text-base">
                                            Show on profile
                                        </Label>
                                        <p class="text-sm text-muted-foreground">
                                            Display this project on your public profile
                                        </p>
                                    </div>
                                    <Checkbox
                                        id="show_project"
                                        v-model:checked="formData.show_project"
                                        class="h-5 w-5"
                                    />
                                </div>
                                <InputError :message="formErrors.show_project" />
                            </div>

                            <div class="flex flex-wrap items-center gap-3 pt-2 lg:col-span-2">
                                <Button type="submit" :disabled="submitting">
                                    {{ submitting ? 'Adding...' : 'Add Project' }}
                                </Button>
                                <Button variant="outline" as-child>
                                    <Link :href="developerProjectsIndex().url">
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
        </div>
    </AppLayout>
</template>
