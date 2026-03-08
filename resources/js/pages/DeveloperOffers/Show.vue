<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Check,
    ExternalLink,
    Mail,
    Send,
    Trash2,
    X,
} from 'lucide-vue-next';
import DeveloperOfferController from '@/actions/App/Http/Controllers/Dashboard/DeveloperOfferController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as developerOffersIndex } from '@/routes/developer-offers';
import type { BreadcrumbItem } from '@/types';

type DeveloperInfo = {
    id: number;
    name: string;
    slug: string | null;
    email: string;
};

type Offer = {
    id: number;
    developer_ids: number[];
    developers: DeveloperInfo[];
    company_name: string;
    job_title_name: string | null;
    message: string;
    salary_range: string | null;
    work_type: string | null;
    contact_email: string;
    status: string;
    status_label: string;
    created_at: string;
    updated_at: string;
    submitted_by: string | null;
    submitted_by_email: string | null;
};

type StatusOption = { value: string; label: string };

type Props = {
    offer: Offer;
    statusOptions: StatusOption[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Developer Offers', href: developerOffersIndex().url },
    { title: 'View offer', href: '#' },
];

function formatDate(iso: string): string {
    return new Date(iso).toLocaleString(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    });
}

function statusVariant(
    status: string,
): 'default' | 'secondary' | 'destructive' | 'outline' {
    if (status === 'approved') return 'default';
    if (status === 'rejected') return 'destructive';
    return 'outline';
}

function setStatus(status: string): void {
    router.put(DeveloperOfferController.update.url(props.offer.id), {
        status,
    });
}

function confirmDelete(): void {
    if (window.confirm('Are you sure you want to delete this offer?')) {
        router.delete(DeveloperOfferController.destroy.url(props.offer.id));
    }
}

function developerUrl(slug: string | null): string {
    if (!slug) return '#';
    return `/developers/${slug}`;
}
</script>

<template>
    <Head :title="`Offer #${offer.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full space-y-6 rounded-xl p-4">
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-3">
                    <Button variant="ghost" size="icon" as-child>
                        <Link :href="developerOffersIndex().url">
                            <ArrowLeft class="size-4" aria-hidden="true" />
                            <span class="sr-only">Back to offers</span>
                        </Link>
                    </Button>
                    <div class="flex items-center gap-3">
                        <div
                            class="flex size-12 items-center justify-center rounded-xl bg-primary/10"
                        >
                            <Send class="size-6 text-primary" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-semibold tracking-tight">
                                Offer #{{ offer.id }}
                            </h1>
                            <p class="text-sm text-muted-foreground">
                                {{ offer.company_name }} ·
                                {{ offer.job_title_name ?? '—' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <Badge :variant="statusVariant(offer.status)">
                        {{ offer.status_label }}
                    </Badge>
                    <template v-if="offer.status === 'pending'">
                        <Button
                            variant="outline"
                            size="sm"
                            class="gap-1 text-green-600 hover:bg-green-50 hover:text-green-700 dark:text-green-400 dark:hover:bg-green-950/50"
                            @click="setStatus('approved')"
                        >
                            <Check class="size-3.5" />
                            Approve
                        </Button>
                        <Button
                            variant="outline"
                            size="sm"
                            class="gap-1 text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-950/50"
                            @click="setStatus('rejected')"
                        >
                            <X class="size-3.5" />
                            Reject
                        </Button>
                    </template>
                    <template v-else>
                        <Button
                            v-if="offer.status !== 'approved'"
                            variant="outline"
                            size="sm"
                            @click="setStatus('approved')"
                        >
                            Approve
                        </Button>
                        <Button
                            v-if="offer.status !== 'rejected'"
                            variant="outline"
                            size="sm"
                            @click="setStatus('rejected')"
                        >
                            Reject
                        </Button>
                    </template>
                    <Button
                        variant="outline"
                        size="sm"
                        class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                        @click="confirmDelete"
                    >
                        <Trash2 class="size-3.5" />
                        Delete
                    </Button>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <Card>
                    <CardHeader>
                        <h3 class="text-sm font-medium text-muted-foreground">
                            Offer details
                        </h3>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p
                                class="text-xs font-medium text-muted-foreground"
                            >
                                Company
                            </p>
                            <p class="mt-0.5 font-medium">
                                {{ offer.company_name }}
                            </p>
                        </div>
                        <div>
                            <p
                                class="text-xs font-medium text-muted-foreground"
                            >
                                Position
                            </p>
                            <p class="mt-0.5 font-medium">
                                {{ offer.job_title_name ?? '—' }}
                            </p>
                        </div>
                        <div v-if="offer.salary_range">
                            <p
                                class="text-xs font-medium text-muted-foreground"
                            >
                                Salary range
                            </p>
                            <p class="mt-0.5 font-medium">
                                {{ offer.salary_range }}
                            </p>
                        </div>
                        <div v-if="offer.work_type">
                            <p
                                class="text-xs font-medium text-muted-foreground"
                            >
                                Work type
                            </p>
                            <p class="mt-0.5 font-medium">
                                {{ offer.work_type }}
                            </p>
                        </div>
                        <div>
                            <p
                                class="text-xs font-medium text-muted-foreground"
                            >
                                Contact email
                            </p>
                            <a
                                :href="`mailto:${offer.contact_email}`"
                                class="mt-0.5 inline-flex items-center gap-1 font-medium text-primary hover:underline"
                            >
                                <Mail class="size-3.5" />
                                {{ offer.contact_email }}
                            </a>
                        </div>
                        <div v-if="offer.submitted_by">
                            <p
                                class="text-xs font-medium text-muted-foreground"
                            >
                                Submitted by
                            </p>
                            <p class="mt-0.5 font-medium">
                                {{ offer.submitted_by }}
                                <span
                                    v-if="offer.submitted_by_email"
                                    class="text-muted-foreground"
                                >
                                    ({{ offer.submitted_by_email }})
                                </span>
                            </p>
                        </div>
                        <div>
                            <p
                                class="text-xs font-medium text-muted-foreground"
                            >
                                Created
                            </p>
                            <p class="mt-0.5 text-sm text-muted-foreground">
                                {{ formatDate(offer.created_at) }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <h3 class="text-sm font-medium text-muted-foreground">
                            Message
                        </h3>
                    </CardHeader>
                    <CardContent>
                        <p
                            class="text-sm whitespace-pre-wrap text-muted-foreground"
                        >
                            {{ offer.message || '—' }}
                        </p>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <h3 class="text-sm font-medium text-muted-foreground">
                        Developers ({{ offer.developers.length }})
                    </h3>
                </CardHeader>
                <CardContent>
                    <ul class="space-y-3">
                        <li
                            v-for="dev in offer.developers"
                            :key="dev.id"
                            class="flex items-center justify-between rounded-lg border border-border px-4 py-3"
                        >
                            <div>
                                <p class="font-medium">{{ dev.name }}</p>
                                <p class="text-sm text-muted-foreground">
                                    {{ dev.email }}
                                </p>
                            </div>
                            <Button
                                v-if="dev.slug"
                                variant="outline"
                                size="sm"
                                as-child
                            >
                                <a
                                    :href="developerUrl(dev.slug)"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="gap-1"
                                >
                                    View profile
                                    <ExternalLink class="size-3.5" />
                                </a>
                            </Button>
                        </li>
                    </ul>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
