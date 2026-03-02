<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Search } from 'lucide-vue-next';
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
import { Button } from '@/components/ui/button';
import { index as hackathonsIndex } from '@/routes/hackathons';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

export type AttendanceSubscriber = {
    id: number;
    developer_id: number;
    developer: { id: number; name: string; slug: string } | null;
};

const props = defineProps<{
    hackathon: { id: number; title: string; start_date: string | null; end_date: string | null };
    dates: string[];
    subscribers: AttendanceSubscriber[];
    attendances: Record<number, Record<string, boolean>>;
    updateUrl: string;
}>();

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string } | undefined);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Hackathons', href: hackathonsIndex().url },
    { title: props.hackathon.title, href: hackathonsIndex().url },
    { title: 'Attendance', href: '#' },
];

function formatDateLabel(iso: string): string {
    return new Date(iso + 'T12:00:00').toLocaleDateString(undefined, {
        month: 'short',
        day: 'numeric',
    });
}

function isAttended(developerId: number, date: string): boolean {
    return Boolean(props.attendances[developerId]?.[date]);
}

function onAttendanceChange(developerId: number, date: string, event: Event): void {
    const target = event.target as HTMLInputElement;
    router.patch(props.updateUrl, {
        developer_id: developerId,
        date,
        attended: target.checked,
    }, { preserveScroll: true });
}

const searchQuery = ref('');
const filteredSubscribers = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    if (!q) return props.subscribers;
    return props.subscribers.filter((s: AttendanceSubscriber) => {
        const name = s.developer?.name?.toLowerCase() ?? '';
        return name.includes(q);
    });
});
</script>

<template>
    <Head :title="`Attendance – ${hackathon.title}`" />

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
                    <h1 class="text-2xl font-semibold tracking-tight">Attendance</h1>
                    <p class="text-muted-foreground">
                        {{ hackathon.title }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Button variant="outline" as-child>
                        <Link :href="`/dashboard/hackathons/${hackathon.id}/subscribers`">
                            ← Subscribers
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

            <div v-if="dates.length > 0 && subscribers.length > 0" class="flex flex-col gap-4">
                <div class="relative w-full sm:max-w-sm">
                    <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Search by subscriber name..."
                        class="pl-9"
                        aria-label="Search subscribers"
                    />
                </div>
                <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="min-w-[180px]">Subscriber</TableHead>
                            <TableHead
                                v-for="d in dates"
                                :key="d"
                                class="w-16 text-center"
                            >
                                {{ formatDateLabel(d) }}
                            </TableHead>
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
                            <TableCell
                                v-for="d in dates"
                                :key="d"
                                class="text-center"
                            >
                                <input
                                    type="checkbox"
                                    :checked="isAttended(s.developer_id, d)"
                                    :aria-label="`${s.developer?.name ?? 'Subscriber'} attended on ${formatDateLabel(d)}`"
                                    class="size-4 cursor-pointer rounded border-input accent-primary"
                                    @change="onAttendanceChange(s.developer_id, d, $event)"
                                />
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
                v-else-if="dates.length === 0"
                class="flex flex-col items-center justify-center rounded-xl border border-dashed py-12"
            >
                <p class="text-center text-sm text-muted-foreground">
                    Set the hackathon start and end dates to track attendance by day.
                </p>
                <Button variant="outline" as-child class="mt-4">
                    <Link :href="`/dashboard/hackathons/${hackathon.id}/edit`">Edit hackathon</Link>
                </Button>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center rounded-xl border border-dashed py-12"
            >
                <p class="text-center text-sm text-muted-foreground">
                    No subscribers yet. Add subscribers first to track attendance.
                </p>
                <Button variant="outline" as-child class="mt-4">
                    <Link :href="`/dashboard/hackathons/${hackathon.id}/subscribers`">View subscribers</Link>
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
