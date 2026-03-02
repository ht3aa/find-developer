<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { MoreHorizontal, Pencil, Plus, Trash2, Users } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { index as hackathonsIndex } from '@/routes/hackathons';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

export type TeamEntry = {
    id: number;
    title: string;
    logo: string | null;
    logo_url: string | null;
    members_count: number;
};

const props = defineProps<{
    hackathon: { id: number; title: string };
    teams: TeamEntry[];
}>();

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string } | undefined);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Hackathons', href: hackathonsIndex().url },
    { title: props.hackathon.title, href: hackathonsIndex().url },
    { title: 'Teams', href: '#' },
];

function confirmDelete(team: TeamEntry): void {
    if (window.confirm(`Delete team "${team.title}"? This will remove all team members.`)) {
        router.delete(`/dashboard/hackathons/${props.hackathon.id}/teams/${team.id}`, {
            preserveScroll: true,
        });
    }
}
</script>

<template>
    <Head :title="`Teams – ${hackathon.title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div
                v-if="flash?.success"
                class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:border-green-800 dark:bg-green-950/50 dark:text-green-200"
            >
                {{ flash.success }}
            </div>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Teams</h1>
                    <p class="text-muted-foreground">
                        {{ hackathon.title }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Button as-child>
                        <Link :href="`/dashboard/hackathons/${hackathon.id}/teams/create`">
                            <Plus class="mr-2 h-4 w-4" />
                            Add team
                        </Link>
                    </Button>
                    <Link
                        :href="hackathonsIndex().url"
                        class="text-sm font-medium text-primary hover:underline"
                    >
                        ← Back to hackathons
                    </Link>
                </div>
            </div>

            <div v-if="teams.length > 0" class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-16">Logo</TableHead>
                            <TableHead>Title</TableHead>
                            <TableHead>Members</TableHead>
                            <TableHead class="w-12" />
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="t in teams" :key="t.id">
                            <TableCell>
                                <img
                                    v-if="t.logo_url"
                                    :src="t.logo_url"
                                    :alt="t.title"
                                    class="h-10 w-10 rounded object-cover"
                                />
                                <span v-else class="text-muted-foreground">—</span>
                            </TableCell>
                            <TableCell class="font-medium">
                                {{ t.title }}
                            </TableCell>
                            <TableCell>
                                <Link
                                    :href="`/dashboard/hackathons/${hackathon.id}/teams/${t.id}/members`"
                                    class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:underline"
                                >
                                    <Users class="size-4 shrink-0" />
                                    {{ t.members_count }} member{{ t.members_count !== 1 ? 's' : '' }}
                                </Link>
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
                                        <DropdownMenuItem as-child>
                                            <Link :href="`/dashboard/hackathons/${hackathon.id}/teams/${t.id}/edit`">
                                                <Pencil class="mr-2 h-4 w-4" />
                                                Edit
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            class="text-destructive focus:text-destructive"
                                            @click="confirmDelete(t)"
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
                <Users class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No teams yet</h3>
                <p class="mb-4 text-center text-sm text-muted-foreground">
                    Create teams for this hackathon and add members.
                </p>
                <Button as-child>
                    <Link :href="`/dashboard/hackathons/${hackathon.id}/teams/create`">
                        <Plus class="mr-2 h-4 w-4" />
                        Add team
                    </Link>
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
