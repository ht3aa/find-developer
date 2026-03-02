<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { MoreHorizontal, Pencil, Plus, Trash2, Trophy } from 'lucide-vue-next';
import HackathonController from '@/actions/App/Http/Controllers/HackathonController';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { index as hackathonsIndex, create as hackathonsCreate } from '@/routes/hackathons';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

export type DashboardHackathonEntry = {
    id: number;
    title: string;
    slug: string;
    body: string | null;
    image: string | null;
    image_url: string | null;
    youtube_url: string | null;
    reward_badge_id: number | null;
    reward_badge: { id: number; name: string } | null;
    reward_description: string | null;
    start_date: string | null;
    end_date: string | null;
    created_at: string | null;
};

type Props = {
    hackathons: DashboardHackathonEntry[];
    can: { updateHackathon?: boolean; deleteHackathon?: boolean };
};

defineProps<Props>();

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string; error?: string } | undefined);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Hackathons', href: hackathonsIndex().url },
];

function formatDate(iso: string | null): string {
    if (!iso) return '';
    return new Date(iso).toLocaleDateString(undefined, { dateStyle: 'medium' });
}

function confirmDelete(h: DashboardHackathonEntry) {
    if (window.confirm(`Are you sure you want to delete "${h.title}"?`)) {
        router.delete(HackathonController.destroy.url(h.id), { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Hackathons" />

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
                    <h1 class="text-2xl font-semibold tracking-tight">Hackathons</h1>
                    <p class="text-muted-foreground">
                        Manage hackathons displayed on the public page
                    </p>
                </div>
                <Button as-child>
                    <Link :href="hackathonsCreate().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Hackathon
                    </Link>
                </Button>
            </div>

            <div v-if="hackathons.length > 0" class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Title</TableHead>
                            <TableHead class="w-24">Image</TableHead>
                            <TableHead>Dates</TableHead>
                            <TableHead>Reward</TableHead>
                            <TableHead>YouTube</TableHead>
                            <TableHead class="w-12" />
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="h in hackathons" :key="h.id">
                            <TableCell class="font-medium">
                                {{ h.title }}
                            </TableCell>
                            <TableCell>
                                <img
                                    v-if="h.image_url"
                                    :src="h.image_url"
                                    :alt="h.title"
                                    class="h-10 w-16 rounded object-cover"
                                />
                                <span v-else class="text-muted-foreground">—</span>
                            </TableCell>
                            <TableCell class="text-muted-foreground text-sm">
                                <span v-if="h.start_date || h.end_date">
                                    {{ h.start_date ?? '…' }} – {{ h.end_date ?? '…' }}
                                </span>
                                <span v-else>—</span>
                            </TableCell>
                            <TableCell class="text-sm">
                                <span v-if="h.reward_badge">{{ h.reward_badge.name }}</span>
                                <span v-else class="text-muted-foreground">—</span>
                            </TableCell>
                            <TableCell>
                                <a
                                    v-if="h.youtube_url"
                                    :href="h.youtube_url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-primary hover:underline"
                                >
                                    Link
                                </a>
                                <span v-else class="text-muted-foreground">—</span>
                            </TableCell>
                            <TableCell>
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" size="icon" class="h-8 w-8">
                                            <span class="sr-only">Open menu</span>
                                            <MoreHorizontal class="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem v-if="can?.updateHackathon" as-child>
                                            <Link :href="HackathonController.edit.url(h.id)">
                                                <Pencil class="mr-2 h-4 w-4" />
                                                Edit
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            v-if="can?.deleteHackathon"
                                            class="text-destructive focus:text-destructive"
                                            @click="confirmDelete(h)"
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
                <Trophy class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No hackathons yet</h3>
                <p class="mb-4 text-center text-sm text-muted-foreground">
                    Get started by creating your first hackathon.
                </p>
                <Button as-child>
                    <Link :href="hackathonsCreate().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Hackathon
                    </Link>
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
