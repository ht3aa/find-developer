<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ThumbsUp } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import DeveloperRecommendationController from '@/actions/App/Http/Controllers/Dashboard/DeveloperRecommendationController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    index as developerRecommendationsIndex,
} from '@/routes/developer-recommendations';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

type RecommendationEdit = {
    id: number;
    recommender_name: string | null;
    recommender_slug: string | null;
    recommended_name: string | null;
    recommended_slug: string | null;
    recommendation_note: string | null;
    status: string;
    created_at: string;
    updated_at: string;
};

type StatusOption = { value: string; label: string };

type Props = {
    recommendation: RecommendationEdit;
    statusOptions: StatusOption[];
};

const props = defineProps<Props>();

const formData = ref({
    status: props.recommendation.status,
    recommendation_note: props.recommendation.recommendation_note ?? '',
});

const submitting = ref(false);
const page = usePage();
const formErrors = computed(() => (page.props.errors as Record<string, string>) ?? {});
const flashSuccess = computed(() => (page.props.flash as { success?: string })?.success);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Recommendations', href: developerRecommendationsIndex().url },
    { title: 'Edit recommendation', href: DeveloperRecommendationController.edit.url(props.recommendation.id) },
];

watch(
    () => props.recommendation,
    (r) => {
        formData.value = {
            status: r.status,
            recommendation_note: r.recommendation_note ?? '',
        };
    },
    { immediate: true },
);

function submit(): void {
    submitting.value = true;
    router.put(
        DeveloperRecommendationController.update.url(props.recommendation.id),
        {
            status: formData.value.status,
            recommendation_note: formData.value.recommendation_note || null,
        },
        {
            preserveScroll: true,
            onFinish: () => { submitting.value = false; },
        },
    );
}

function formatDate(iso: string): string {
    return new Date(iso).toLocaleString(undefined, { dateStyle: 'short', timeStyle: 'short' });
}
</script>

<template>
    <Head title="Edit recommendation" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full space-y-6 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                    <ThumbsUp class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <Heading
                        title="Edit recommendation"
                        description="Update status and note (super admin only)"
                    />
                </div>
            </div>

            <Card class="lg:col-span-2">
                <CardHeader class="pb-4">
                    <h3 class="text-sm font-medium text-muted-foreground">Recommendation details</h3>
                    <p class="mt-1 text-sm text-muted-foreground">
                        From <strong>{{ recommendation.recommender_name ?? '—' }}</strong>
                        to <strong>{{ recommendation.recommended_name ?? '—' }}</strong>
                        · Created {{ formatDate(recommendation.created_at) }}
                    </p>
                </CardHeader>
                <CardContent>
                    <form class="space-y-6" @submit.prevent="submit">
                        <div class="grid gap-2">
                            <Label for="status">Status <span class="text-destructive">*</span></Label>
                            <select
                                id="status"
                                v-model="formData.status"
                                required
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            >
                                <option
                                    v-for="opt in statusOptions"
                                    :key="opt.value"
                                    :value="opt.value"
                                >
                                    {{ opt.label }}
                                </option>
                            </select>
                            <InputError :message="formErrors.status" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="recommendation_note">Note</Label>
                            <textarea
                                id="recommendation_note"
                                v-model="formData.recommendation_note"
                                rows="4"
                                maxlength="2000"
                                placeholder="Optional admin note"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            />
                            <InputError :message="formErrors.recommendation_note" />
                            <p class="text-muted-foreground text-xs">
                                {{ formData.recommendation_note.length }} / 2000
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <Button type="submit" :disabled="submitting">
                                {{ submitting ? 'Updating...' : 'Update recommendation' }}
                            </Button>
                            <Button variant="outline" as-child>
                                <Link :href="developerRecommendationsIndex().url">Cancel</Link>
                            </Button>
                            <span
                                v-show="flashSuccess"
                                class="inline-flex items-center gap-1.5 rounded-md bg-green-500/10 px-2.5 py-1 text-sm font-medium text-green-700 dark:text-green-400"
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-green-500" />
                                {{ flashSuccess }}
                            </span>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
