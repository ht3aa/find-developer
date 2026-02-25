<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ExternalLink, FileText, MoreHorizontal, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import DeveloperBlogController from '@/actions/App/Http/Controllers/Dashboard/DeveloperBlogController';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { index as developerBlogsIndex, create as developerBlogsCreate } from '@/routes/developer-blogs';
import { dashboard } from '@/routes';
import type { DeveloperBlogEntry } from '@/types/developer-blog';
import type { BreadcrumbItem } from '@/types';

type Props = {
    blogs: DeveloperBlogEntry[];
    can?: { updateDeveloperBlog?: boolean; deleteDeveloperBlog?: boolean };
};

const props = defineProps<Props>();

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string; error?: string } | undefined);
const can = computed(() => ({
    ...((page.props.auth as { can?: Props['can'] })?.can ?? {}),
    ...(props.can ?? {}),
}));

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Blogs', href: developerBlogsIndex().url },
];

function confirmDelete(blog: DeveloperBlogEntry) {
    if (window.confirm(`Are you sure you want to delete "${blog.title}"?`)) {
        router.delete(DeveloperBlogController.destroy.url(blog.id), { preserveScroll: true });
    }
}

function statusVariant(status: string): 'default' | 'secondary' | 'destructive' | 'outline' {
    if (status === 'published') return 'default';
    if (status === 'archived') return 'secondary';
    return 'outline';
}
</script>

<template>
    <Head title="Blogs" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div
                v-if="flash?.success"
                class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:border-green-800 dark:bg-green-950/50 dark:text-green-200"
            >
                {{ flash.success }}
            </div>
            <div
                v-if="flash?.error"
                class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 dark:border-red-800 dark:bg-red-950/50 dark:text-red-200"
            >
                {{ flash.error }}
            </div>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Blogs</h1>
                    <p class="text-muted-foreground">Manage your blog posts</p>
                </div>
                <Button as-child>
                    <Link :href="developerBlogsCreate().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Add post
                    </Link>
                </Button>
            </div>

            <div v-if="blogs.length > 0" class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Title</TableHead>
                            <TableHead class="w-[100px]">Status</TableHead>
                            <TableHead class="w-[140px]">Published</TableHead>
                            <TableHead class="w-[80px]" />
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="blog in blogs" :key="blog.id">
                            <TableCell class="font-medium">
                                {{ blog.title }}
                            </TableCell>
                            <TableCell>
                                <Badge :variant="statusVariant(blog.status)">
                                    {{ blog.status_label }}
                                </Badge>
                            </TableCell>
                            <TableCell class="text-muted-foreground text-sm">
                                {{ blog.published_at ?? '—' }}
                            </TableCell>
                            <TableCell>
                                <DropdownMenu v-if="can.updateDeveloperBlog !== false || can.deleteDeveloperBlog !== false">
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" size="icon" class="h-8 w-8">
                                            <span class="sr-only">Open menu</span>
                                            <MoreHorizontal class="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem v-if="can.updateDeveloperBlog !== false" as-child>
                                            <Link :href="DeveloperBlogController.edit.url(blog.id)">
                                                <Pencil class="mr-2 h-4 w-4" />
                                                Edit
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            v-if="can.deleteDeveloperBlog !== false"
                                            class="text-destructive focus:text-destructive"
                                            @click="confirmDelete(blog)"
                                        >
                                            <Trash2 class="mr-2 h-4 w-4" />
                                            Delete
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center rounded-xl border border-dashed py-12"
            >
                <FileText class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No blog posts yet</h3>
                <p class="mb-4 text-center text-sm text-muted-foreground">
                    Add your first post to share your thoughts with the community.
                </p>
                <Button as-child>
                    <Link :href="developerBlogsCreate().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Add post
                    </Link>
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
