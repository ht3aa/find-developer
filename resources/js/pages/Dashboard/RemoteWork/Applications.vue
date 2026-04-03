<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
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
import developersRoutes from '@/routes/developers';
import { dashboard } from '@/routes';
import remoteWorkDashboard from '@/routes/dashboard/remote-work';
import applicationRoutes from '@/routes/dashboard/remote-work/applications';
import type { BreadcrumbItem } from '@/types';

type DeveloperRow = {
    id: number;
    slug?: string | null;
    name: string;
    email: string | null;
    status?: string;
    job_title?: { name: string } | null;
    user?: { name: string; email: string } | null;
};

type ApplicationRow = {
    id: number;
    status: string;
    cover_message: string | null;
    developer: DeveloperRow;
};

type Paginated = {
    data: ApplicationRow[];
};

const props = defineProps<{
    job: { id: number; title: string; slug: string; status: string };
    applications: Paginated;
}>();

const page = usePage();
const flash = computed(
    () => (page.props.flash as { success?: string })?.success,
);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Remote work', href: remoteWorkDashboard.index.url() },
    {
        title: props.job.title,
        href: remoteWorkDashboard.applications.url({ job: props.job.slug }),
    },
];

function accept(id: number): void {
    router.post(applicationRoutes.accept.url({ application: id }), {}, { preserveScroll: true });
}

function reject(id: number): void {
    router.post(applicationRoutes.reject.url({ application: id }), {}, { preserveScroll: true });
}

function hasPublicProfile(dev: DeveloperRow): boolean {
    return dev.status === 'approved' || dev.status === 'experience_changed';
}

/** Wayfinder `show.url()` throws if the slug argument is null/undefined. */
function canLinkToPublicProfile(dev: DeveloperRow | null | undefined): boolean {
    if (!dev?.slug) {
        return false;
    }
    return hasPublicProfile(dev);
}
</script>

<template>
    <Head :title="`Applications — ${job.title}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full space-y-6 p-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold tracking-tight">Applications</h1>
                    <p class="text-sm text-muted-foreground">{{ job.title }}</p>
                </div>
                <Button variant="outline" as-child>
                    <Link :href="remoteWorkDashboard.index.url()">Back to posts</Link>
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
                            <TableHead>Developer</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Profile</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="app in applications.data" :key="app.id">
                            <TableCell class="font-medium">
                                {{ app.developer.name }}
                                <span
                                    v-if="app.developer.job_title"
                                    class="block text-xs text-muted-foreground"
                                >
                                    {{ app.developer.job_title.name }}
                                </span>
                            </TableCell>
                            <TableCell class="text-sm">
                                {{ app.developer.user?.email ?? app.developer.email ?? '—' }}
                            </TableCell>
                            <TableCell>
                                <Button
                                    v-if="canLinkToPublicProfile(app.developer)"
                                    variant="link"
                                    class="h-auto p-0"
                                    as-child
                                >
                                    <Link
                                        :href="developersRoutes.show.url(String(app.developer.slug))"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        View profile
                                    </Link>
                                </Button>
                                <span
                                    v-else
                                    class="text-xs text-muted-foreground"
                                >
                                    Not listed publicly
                                </span>
                            </TableCell>
                            <TableCell>{{ app.status }}</TableCell>
                            <TableCell class="text-right">
                                <template v-if="app.status === 'pending'">
                                    <Button size="sm" class="mr-2" @click="accept(app.id)">
                                        Accept
                                    </Button>
                                    <Button size="sm" variant="outline" @click="reject(app.id)">
                                        Reject
                                    </Button>
                                </template>
                                <span v-else class="text-muted-foreground">—</span>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="applications.data.length === 0">
                            <TableCell colspan="5" class="text-center text-muted-foreground">
                                No applications yet.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
            <div
                v-if="applications.data.some((a) => a.cover_message)"
                class="space-y-4"
            >
                <h2 class="text-sm font-semibold">Messages</h2>
                <div
                    v-for="app in applications.data"
                    :key="`msg-${app.id}`"
                    class="rounded-lg border border-border p-4"
                >
                    <p class="text-sm font-medium">{{ app.developer.name }}</p>
                    <p v-if="app.cover_message" class="mt-2 whitespace-pre-wrap text-sm text-muted-foreground">
                        {{ app.cover_message }}
                    </p>
                    <p v-else class="mt-2 text-sm text-muted-foreground">No message.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
