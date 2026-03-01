<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Mail } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import Pagination from '@/components/Pagination.vue';
import { index as newsletterIndex } from '@/routes/dashboard/newsletter';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

type Subscriber = {
    id: number;
    email: string;
    subscribed_at: string;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type Props = {
    subscribers: {
        data: Subscriber[];
        links: PaginationLink[];
        current_page: number;
        last_page: number;
        total: number;
        from: number | null;
        to: number | null;
    };
};

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Newsletter', href: newsletterIndex().url },
];

function formatDate(iso: string): string {
    const d = new Date(iso);
    return d.toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}
</script>

<template>
    <Head title="Newsletter" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Newsletter
                    </h1>
                    <p class="text-muted-foreground">
                        Subscriber emails (super admin only)
                    </p>
                </div>
            </div>

            <div
                v-if="subscribers.data.length > 0"
                class="rounded-md border"
            >
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[80px]">#</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead class="w-[180px]">Subscribed at</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="(sub, index) in subscribers.data"
                            :key="sub.id"
                        >
                            <TableCell class="text-muted-foreground text-sm">
                                {{ subscribers.from != null ? subscribers.from + index : index + 1 }}
                            </TableCell>
                            <TableCell class="font-medium">
                                {{ sub.email }}
                            </TableCell>
                            <TableCell class="text-muted-foreground text-sm whitespace-nowrap">
                                {{ formatDate(sub.subscribed_at) }}
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center rounded-xl border border-dashed py-12"
            >
                <Mail class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No subscribers yet</h3>
                <p class="text-center text-sm text-muted-foreground">
                    Newsletter signups from the hero section will appear here.
                </p>
            </div>

            <Pagination
                v-if="subscribers.links?.length"
                :links="subscribers.links"
            />
        </div>
    </AppLayout>
</template>
