<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ExternalLink, GitBranch } from 'lucide-vue-next';
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
import giteaRepositories from '@/routes/dashboard/gitea-repositories';
import type { BreadcrumbItem } from '@/types';

type RepoRow = {
    application_id: number;
    job_title: string;
    job_slug: string;
    repo_url: string | null;
    gitea_owner: string;
    gitea_repo_name: string;
    provisioned_at: string | null;
};

defineProps<{
    repositories: RepoRow[];
    giteaBaseUrlConfigured: boolean;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Gitea repositories', href: giteaRepositories.index.url() },
];
</script>

<template>
    <Head title="Gitea repositories" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full space-y-6 p-4">
            <div class="flex flex-wrap items-center gap-3">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10"
                >
                    <GitBranch class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <h1 class="text-xl font-semibold tracking-tight">
                        Gitea repositories
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Remote work projects where you were accepted and given access to the
                        client’s private repository.
                    </p>
                </div>
            </div>

            <p
                v-if="!giteaBaseUrlConfigured"
                class="rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/50 dark:text-amber-200"
            >
                Gitea base URL is not configured in this environment. Links may be unavailable
                until <code class="rounded bg-muted px-1">GITEA_URL</code> is set.
            </p>

            <div class="rounded-xl border border-border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Remote work post</TableHead>
                            <TableHead>Repository</TableHead>
                            <TableHead class="text-right">Open</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="row in repositories" :key="row.application_id">
                            <TableCell class="font-medium">
                                {{ row.job_title }}
                            </TableCell>
                            <TableCell class="text-muted-foreground">
                                <span class="font-mono text-sm"
                                    >{{ row.gitea_owner }}/{{ row.gitea_repo_name }}</span
                                >
                            </TableCell>
                            <TableCell class="text-right">
                                <Button
                                    v-if="row.repo_url"
                                    variant="outline"
                                    size="sm"
                                    class="gap-1"
                                    as-child
                                >
                                    <a
                                        :href="row.repo_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <ExternalLink class="h-3.5 w-3.5" />
                                        Gitea
                                    </a>
                                </Button>
                                <span v-else class="text-xs text-muted-foreground">—</span>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="repositories.length === 0">
                            <TableCell
                                colspan="3"
                                class="py-10 text-center text-muted-foreground"
                            >
                                No Gitea repositories yet. When a client accepts you on a remote
                                work post with a provisioned repo, it will appear here.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
