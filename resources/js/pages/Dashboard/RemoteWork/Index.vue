<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Briefcase, Plus } from 'lucide-vue-next';
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import remoteWorkDashboard from '@/routes/dashboard/remote-work';
import type { BreadcrumbItem } from '@/types';

type JobRow = {
    id: number;
    title: string;
    slug: string;
    status: string;
    created_at: string | null;
    gitea_repository_url: string | null;
};

type Paginated = {
    data: JobRow[];
    links: { url: string | null; label: string; active: boolean }[];
};

defineProps<{
    jobs: Paginated;
}>();

const page = usePage();
const flash = computed(
    () => (page.props.flash as { success?: string })?.success,
);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Remote work', href: remoteWorkDashboard.index.url() },
];

function statusVariant(
    s: string,
): 'default' | 'secondary' | 'destructive' | 'outline' {
    if (s === 'approved') {
        return 'default';
    }
    if (s === 'rejected') {
        return 'destructive';
    }
    return 'secondary';
}
</script>

<template>
    <Head title="Remote work posts" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full space-y-6 p-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10"
                    >
                        <Briefcase class="h-6 w-6 text-primary" />
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold tracking-tight">
                            Remote work posts
                        </h1>
                        <p class="text-sm text-muted-foreground">
                            Posts you created. Pending posts await payment verification and
                            approval.
                        </p>
                    </div>
                </div>
                <Button as-child>
                    <Link :href="remoteWorkDashboard.create.url()">
                        <Plus class="mr-2 h-4 w-4" />
                        New post
                    </Link>
                </Button>
            </div>
            <p
                v-if="flash"
                class="rounded-md border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-800 dark:border-green-900 dark:bg-green-950 dark:text-green-200"
            >
                {{ flash }}
            </p>
            <div class="rounded-xl border border-border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Title</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead class="max-w-md">Gitea repository</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="job in jobs.data" :key="job.id">
                            <TableCell class="font-medium">{{ job.title }}</TableCell>
                            <TableCell>
                                <Badge :variant="statusVariant(job.status)">
                                    {{ job.status }}
                                </Badge>
                            </TableCell>
                            <TableCell class="max-w-md break-all text-sm">
                                <a
                                    v-if="job.gitea_repository_url"
                                    :href="job.gitea_repository_url"
                                    class="text-primary underline-offset-4 hover:underline"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    {{ job.gitea_repository_url }}
                                </a>
                                <span v-else class="text-muted-foreground">—</span>
                            </TableCell>
                            <TableCell class="text-right">
                                <Button v-if="job.status === 'pending'" as-child variant="outline" size="sm">
                                    <Link :href="remoteWorkDashboard.edit.url(job.slug)">
                                        Edit
                                    </Link>
                                </Button>
                                <Button as-child variant="outline" size="sm" class="ml-2">
                                    <Link
                                        :href="remoteWorkDashboard.applications.url({ job: job.slug })"
                                    >
                                        Applications
                                    </Link>
                                </Button>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="jobs.data.length === 0">
                            <TableCell colspan="4" class="text-center text-muted-foreground">
                                No posts yet.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
