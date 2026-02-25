<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { FileText } from 'lucide-vue-next';
import DeveloperBlogController from '@/actions/App/Http/Controllers/Dashboard/DeveloperBlogController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    index as developerBlogsIndex,
    create as developerBlogsCreate,
} from '@/routes/developer-blogs';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

type StatusOption = { value: string; label: string };

type Props = {
    statusOptions: StatusOption[];
    canChangeStatus?: boolean;
};

const props = defineProps<Props>();

const formData = ref({
    title: '',
    excerpt: '',
    content: '',
    featured_image: '',
    status: 'draft',
    published_at: '',
});

const submitting = ref(false);
const page = usePage();
const formErrors = computed(() => (page.props.errors as Record<string, string>) ?? {});
const flashSuccess = computed(() => (page.props.flash as { success?: string })?.success);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Blogs', href: developerBlogsIndex().url },
    { title: 'Add', href: developerBlogsCreate().url },
];

function submit(): void {
    const d = formData.value;
    const payload: Record<string, unknown> = {
        title: d.title,
        excerpt: d.excerpt || null,
        content: d.content,
        featured_image: d.featured_image || null,
    };
    if (props.canChangeStatus) {
        payload.status = d.status;
        payload.published_at = d.published_at || null;
    }
    submitting.value = true;
    router.post(DeveloperBlogController.store.url(), payload, {
        preserveScroll: true,
        onFinish: () => { submitting.value = false; },
    });
}
</script>

<template>
    <Head title="Add blog post" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full space-y-6 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                    <FileText class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <Heading title="Add blog post" description="Write a new post" />
                </div>
            </div>

            <Card class="lg:col-span-2">
                <CardHeader class="pb-4">
                    <h3 class="text-sm font-medium text-muted-foreground">Post details</h3>
                </CardHeader>
                <CardContent>
                    <form class="space-y-6" @submit.prevent="submit">
                        <div class="grid gap-2">
                            <Label for="title">Title <span class="text-destructive">*</span></Label>
                            <Input
                                id="title"
                                v-model="formData.title"
                                required
                                placeholder="Post title"
                                class="transition-colors focus-visible:ring-2"
                            />
                            <InputError :message="formErrors.title" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="excerpt">Excerpt</Label>
                            <textarea
                                id="excerpt"
                                v-model="formData.excerpt"
                                rows="2"
                                placeholder="Short summary"
                                class="flex min-h-[60px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            />
                            <InputError :message="formErrors.excerpt" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="content">Content <span class="text-destructive">*</span></Label>
                            <textarea
                                id="content"
                                v-model="formData.content"
                                required
                                rows="12"
                                placeholder="Write your post..."
                                class="flex min-h-[200px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            />
                            <InputError :message="formErrors.content" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="featured_image">Featured image (path)</Label>
                            <Input
                                id="featured_image"
                                v-model="formData.featured_image"
                                placeholder="Optional path or URL"
                            />
                            <InputError :message="formErrors.featured_image" />
                        </div>
                        <div
                            v-if="canChangeStatus"
                            class="grid gap-2 sm:grid-cols-2"
                        >
                            <div class="grid gap-2">
                                <Label for="status">Status</Label>
                                <select
                                    id="status"
                                    v-model="formData.status"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                >
                                    <option
                                        v-for="opt in statusOptions"
                                        :key="opt.value"
                                        :value="opt.value"
                                    >
                                        {{ opt.label }}
                                    </option>
                                </select>
                                <InputError :message="formErrors.status" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="published_at">Published at</Label>
                                <Input
                                    id="published_at"
                                    v-model="formData.published_at"
                                    type="datetime-local"
                                />
                                <InputError :message="formErrors.published_at" />
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <Button type="submit" :disabled="submitting">
                                {{ submitting ? 'Creating...' : 'Create post' }}
                            </Button>
                            <Button variant="outline" as-child>
                                <Link :href="developerBlogsIndex().url">Cancel</Link>
                            </Button>
                            <span
                                v-show="flashSuccess"
                                class="inline-flex items-center gap-1.5 rounded-md bg-green-500/10 px-2.5 py-1 text-sm font-medium text-green-700 dark:text-green-400"
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-green-500" />
                                {{ flashSuccess }}
                            </span>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
