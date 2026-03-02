<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Pencil, Plus, Trash2, Users } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { index as hackathonsIndex } from '@/routes/hackathons';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

export type MemberEntry = {
    id: number;
    developer_id: number;
    developer: { id: number; name: string; slug: string; email: string | null } | null;
    position: string;
    position_label: string;
};

const props = defineProps<{
    hackathon: { id: number; title: string };
    team: { id: number; title: string };
    members: MemberEntry[];
}>();

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string } | undefined);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Hackathons', href: hackathonsIndex().url },
    { title: props.hackathon.title, href: '#' },
    { title: 'Teams', href: `/dashboard/hackathons/${props.hackathon.id}/teams` },
    { title: props.team.title, href: '#' },
];

function confirmDelete(member: MemberEntry): void {
    const name = member.developer?.name ?? 'this member';
    if (window.confirm(`Remove ${name} from the team?`)) {
        router.delete(
            `/dashboard/hackathons/${props.hackathon.id}/teams/${props.team.id}/members/${member.id}`,
            { preserveScroll: true }
        );
    }
}
</script>

<template>
    <Head :title="`${team.title} – Members`" />

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
                    <h1 class="text-2xl font-semibold tracking-tight">Team members</h1>
                    <p class="text-muted-foreground">
                        {{ team.title }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Button as-child>
                        <Link :href="`/dashboard/hackathons/${hackathon.id}/teams/${team.id}/members/create`">
                            <Plus class="mr-2 h-4 w-4" />
                            Add member
                        </Link>
                    </Button>
                    <Link
                        :href="`/dashboard/hackathons/${hackathon.id}/teams`"
                        class="text-sm font-medium text-primary hover:underline"
                    >
                        ← Back to teams
                    </Link>
                </div>
            </div>

            <div v-if="members.length > 0" class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Developer</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Position</TableHead>
                            <TableHead class="w-12" />
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="m in members" :key="m.id">
                            <TableCell class="font-medium">
                                <Link
                                    v-if="m.developer"
                                    :href="`/developers/${m.developer.slug}`"
                                    class="text-primary hover:underline"
                                >
                                    {{ m.developer.name }}
                                </Link>
                                <span v-else class="text-muted-foreground">—</span>
                            </TableCell>
                            <TableCell class="text-muted-foreground text-sm">
                                <a
                                    v-if="m.developer?.email"
                                    :href="`mailto:${m.developer.email}`"
                                    class="hover:underline"
                                >
                                    {{ m.developer.email }}
                                </a>
                                <span v-else>—</span>
                            </TableCell>
                            <TableCell>
                                <Badge :variant="m.position === 'team_leader' ? 'default' : 'secondary'">
                                    {{ m.position_label }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Link
                                        :href="`/dashboard/hackathons/${hackathon.id}/teams/${team.id}/members/${m.id}/edit`"
                                        class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:underline"
                                    >
                                        <Pencil class="size-4 shrink-0" />
                                        Edit
                                    </Link>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1.5 text-sm font-medium text-destructive hover:underline"
                                        @click="confirmDelete(m)"
                                    >
                                        <Trash2 class="size-4 shrink-0" />
                                        Remove
                                    </button>
                                </div>
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
                <h3 class="mb-2 text-lg font-semibold">No members yet</h3>
                <p class="mb-4 text-center text-sm text-muted-foreground">
                    Add developers to this team.
                </p>
                <Button as-child>
                    <Link :href="`/dashboard/hackathons/${hackathon.id}/teams/${team.id}/members/create`">
                        <Plus class="mr-2 h-4 w-4" />
                        Add member
                    </Link>
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
