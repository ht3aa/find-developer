<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ArrowLeft, ClipboardList, User, Box, Calendar, Tag } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { index as activityLogIndex } from '@/routes/dashboard/activity-log';
import { dashboard } from '@/routes';
import type { ActivityLogDetail } from '@/types/activity-log';
import type { BreadcrumbItem } from '@/types';

type Props = {
    activity: ActivityLogDetail;
};

const props = defineProps<Props>();

function formatDate(iso: string): string {
    const d = new Date(iso);
    return d.toLocaleString(undefined, {
        dateStyle: 'medium',
        timeStyle: 'medium',
    });
}

const propertiesJson = computed(() =>
    JSON.stringify(props.activity.properties, null, 2),
);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Activity Log', href: activityLogIndex().url },
    { title: `#${props.activity.id}`, href: '#' },
];
</script>

<template>
    <Head :title="`Activity #${activity.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full space-y-6 rounded-xl p-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10"
                    >
                        <ClipboardList class="h-6 w-6 text-primary" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight">
                            Activity #{{ activity.id }}
                        </h1>
                        <p class="text-muted-foreground">
                            {{ activity.description }}
                        </p>
                    </div>
                </div>
                <Button variant="outline" as-child>
                    <Link :href="activityLogIndex().url">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Back to log
                    </Link>
                </Button>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader class="pb-2">
                        <h3 class="flex items-center gap-2 text-sm font-medium text-muted-foreground">
                            <Tag class="size-4" />
                            Event &amp; log
                        </h3>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <Badge
                                v-if="activity.event"
                                :variant="
                                    activity.event === 'created'
                                        ? 'default'
                                        : activity.event === 'deleted'
                                          ? 'destructive'
                                          : 'secondary'
                                "
                            >
                                {{ activity.event }}
                            </Badge>
                            <span
                                v-if="activity.log_name"
                                class="rounded bg-muted px-2 py-0.5 text-xs font-mono"
                            >
                                {{ activity.log_name }}
                            </span>
                            <span
                                v-if="activity.batch_uuid"
                                class="rounded bg-muted px-2 py-0.5 text-xs font-mono"
                                :title="activity.batch_uuid"
                            >
                                batch
                            </span>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <h3 class="flex items-center gap-2 text-sm font-medium text-muted-foreground">
                            <Calendar class="size-4" />
                            Timestamps
                        </h3>
                    </CardHeader>
                    <CardContent class="space-y-2 text-sm">
                        <p>
                            <span class="text-muted-foreground">Created:</span>
                            {{ formatDate(activity.created_at) }}
                        </p>
                        <p>
                            <span class="text-muted-foreground">Updated:</span>
                            {{ formatDate(activity.updated_at) }}
                        </p>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader class="pb-2">
                        <h3 class="flex items-center gap-2 text-sm font-medium text-muted-foreground">
                            <Box class="size-4" />
                            Subject
                        </h3>
                    </CardHeader>
                    <CardContent class="space-y-2 text-sm">
                        <p v-if="activity.subject_type_short">
                            <span class="text-muted-foreground">Type:</span>
                            <code class="rounded bg-muted px-1.5 py-0.5 font-mono text-xs">{{ activity.subject_type_short }}</code>
                        </p>
                        <p v-if="activity.subject_id !== null">
                            <span class="text-muted-foreground">ID:</span>
                            {{ activity.subject_id }}
                        </p>
                        <p v-if="activity.subject_label">
                            <span class="text-muted-foreground">Label:</span>
                            {{ activity.subject_label }}
                        </p>
                        <p
                            v-if="!activity.subject_type && activity.subject_id === null"
                            class="text-muted-foreground"
                        >
                            —
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <h3 class="flex items-center gap-2 text-sm font-medium text-muted-foreground">
                            <User class="size-4" />
                            Causer
                        </h3>
                    </CardHeader>
                    <CardContent class="space-y-2 text-sm">
                        <p v-if="activity.causer_type_short">
                            <span class="text-muted-foreground">Type:</span>
                            <code class="rounded bg-muted px-1.5 py-0.5 font-mono text-xs">{{ activity.causer_type_short }}</code>
                        </p>
                        <p v-if="activity.causer_id !== null">
                            <span class="text-muted-foreground">ID:</span>
                            {{ activity.causer_id }}
                        </p>
                        <p v-if="activity.causer_name">
                            <span class="text-muted-foreground">Name:</span>
                            {{ activity.causer_name }}
                        </p>
                        <p
                            v-if="!activity.causer_type && activity.causer_id === null"
                            class="text-muted-foreground"
                        >
                            —
                        </p>
                    </CardContent>
                </Card>
            </div>

            <Card v-if="Object.keys(activity.properties).length > 0">
                <CardHeader class="pb-2">
                    <h3 class="text-sm font-medium text-muted-foreground">
                        Properties (attributes / old values)
                    </h3>
                </CardHeader>
                <CardContent>
                    <pre
                        class="max-h-[420px] overflow-auto rounded-lg border bg-muted/30 p-4 text-xs font-mono leading-relaxed"
                    >{{ propertiesJson }}</pre>
                </CardContent>
            </Card>

            <Card v-else>
                <CardContent class="py-8 text-center text-sm text-muted-foreground">
                    No properties recorded for this activity.
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
