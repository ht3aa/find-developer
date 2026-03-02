<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Pencil, Plus, Search, Users } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { Input } from '@/components/ui/input';
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

export type SubscriberEntry = {
    id: number;
    developer: { id: number; name: string; slug: string; email: string | null } | null;
    message: string;
    status: string;
    status_label: string;
    created_at: string | null;
};

const props = defineProps<{
    hackathon: { id: number; title: string; start_date?: string | null; end_date?: string | null };
    subscribers: SubscriberEntry[];
}>();

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string } | undefined);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Hackathons', href: hackathonsIndex().url },
    { title: props.hackathon.title, href: hackathonsIndex().url },
    { title: 'Subscribers', href: '#' },
];

function formatDate(iso: string | null): string {
    if (!iso) return '';
    return new Date(iso).toLocaleString(undefined, { dateStyle: 'medium', timeStyle: 'short' });
}

function statusVariant(status: string): 'default' | 'secondary' | 'outline' | 'destructive' {
    switch (status) {
        case 'confirmed':
            return 'default';
        case 'cancelled':
            return 'destructive';
        default:
            return 'secondary';
    }
}

const hasAttendanceDates = computed(() =>
    Boolean(props.hackathon.start_date && props.hackathon.end_date)
);

const searchQuery = ref('');
const filteredSubscribers = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    if (!q) return props.subscribers;
    return props.subscribers.filter((s: SubscriberEntry) => {
        const name = s.developer?.name?.toLowerCase() ?? '';
        const email = s.developer?.email?.toLowerCase() ?? '';
        const message = s.message?.toLowerCase() ?? '';
        const status = s.status_label?.toLowerCase() ?? '';
        return name.includes(q) || email.includes(q) || message.includes(q) || status.includes(q);
    });
});
</script>

<template>
    <Head :title="`Subscribers – ${hackathon.title}`" />

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
                    <h1 class="text-2xl font-semibold tracking-tight">Subscribers</h1>
                    <p class="text-muted-foreground">
                        {{ hackathon.title }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Button v-if="hasAttendanceDates" as-child>
                        <Link :href="`/dashboard/hackathons/${hackathon.id}/attendance`">
                            Attendance
                        </Link>
                    </Button>
                    <Button as-child>
                        <Link :href="`/dashboard/hackathons/${hackathon.id}/subscribers/create`">
                            <Plus class="mr-2 h-4 w-4" />
                            Add subscriber
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

            <div v-if="subscribers.length > 0" class="flex flex-col gap-4">
                <div class="relative w-full sm:max-w-sm">
                    <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Search by name, email, message, status..."
                        class="pl-9"
                        aria-label="Search subscribers"
                    />
                </div>
                <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Developer</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Message</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Subscribed at</TableHead>
                            <TableHead class="w-12" />
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="s in filteredSubscribers" :key="s.id">
                            <TableCell class="font-medium">
                                <Link
                                    v-if="s.developer"
                                    :href="`/developers/${s.developer.slug}`"
                                    class="text-primary hover:underline"
                                >
                                    {{ s.developer.name }}
                                </Link>
                                <span v-else class="text-muted-foreground">—</span>
                            </TableCell>
                            <TableCell class="text-muted-foreground text-sm">
                                <a
                                    v-if="s.developer?.email"
                                    :href="`mailto:${s.developer.email}`"
                                    class="hover:underline"
                                >
                                    {{ s.developer.email }}
                                </a>
                                <span v-else>—</span>
                            </TableCell>
                            <TableCell class="max-w-xs truncate text-sm text-muted-foreground">
                                {{ s.message }}
                            </TableCell>
                            <TableCell>
                                <Badge :variant="statusVariant(s.status)">
                                    {{ s.status_label }}
                                </Badge>
                            </TableCell>
                            <TableCell class="text-muted-foreground text-sm">
                                {{ formatDate(s.created_at) }}
                            </TableCell>
                            <TableCell>
                                <Link
                                    :href="`/dashboard/hackathons/${hackathon.id}/subscribers/${s.id}/edit`"
                                    class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:underline"
                                >
                                    <Pencil class="size-4 shrink-0" />
                                    Edit
                                </Link>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
                </div>
                <p
                    v-if="filteredSubscribers.length === 0"
                    class="text-sm text-muted-foreground"
                >
                    No subscribers match your search.
                </p>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center rounded-xl border border-dashed py-12"
            >
                <Users class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No subscribers yet</h3>
                <p class="text-center text-sm text-muted-foreground">
                    Developers who register for this hackathon will appear here.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
