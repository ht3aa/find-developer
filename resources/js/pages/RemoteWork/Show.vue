<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ChevronRight, UserPlus } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Footer from '@/components/Footer.vue';
import Hero from '@/components/Hero.vue';
import Navbar from '@/components/Navbar.vue';
import SeoHead from '@/components/SeoHead.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { login } from '@/routes';
import remoteWorkRoutes from '@/routes/remote-work';

type JobShow = {
    id: number;
    title: string;
    slug: string;
    description: string;
    company_name: string;
    requirements: string | null;
    salary_from: number | null;
    salary_to: number | null;
    salary_currency: string | null;
    location: string | null;
    location_label?: string | null;
    job_title?: { name: string } | null;
    created_at: string | null;
};

const props = defineProps<{
    job: JobShow;
    canApply: boolean;
    hasApplied: boolean;
}>();

const applyForm = useForm({
    cover_message: '',
});

const participationSection = ref<HTMLElement | null>(null);

function scrollToParticipation(): void {
    participationSection.value?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function submitApplication(): void {
    applyForm.post(remoteWorkRoutes.apply.url({ companyJob: props.job.slug }));
}

const page = usePage();
const flash = computed(
    () => page.props.flash as { success?: string; error?: string } | undefined,
);
const auth = computed(() => page.props.auth as { user?: unknown } | undefined);

const locationDisplay = computed(
    () => props.job.location_label ?? props.job.location,
);
</script>

<template>
    <SeoHead
        :title="job.title"
        :description="job.description.slice(0, 160)"
        :canonical="remoteWorkRoutes.show.url(job.slug)"
    />
    <Head :title="job.title" />
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />
        <Hero
            badge="Remote work"
            :title="job.title"
            :description="job.company_name"
        />
        <article
            class="mx-auto w-full max-w-3xl px-4 py-8 sm:px-6 lg:px-8"
        >
            <nav class="mb-8 flex flex-wrap items-center gap-1 text-sm text-muted-foreground">
                <Link
                    :href="remoteWorkRoutes.index.url()"
                    class="hover:text-foreground"
                >
                    Remote work
                </Link>
                <ChevronRight class="h-4 w-4 shrink-0 opacity-60" aria-hidden="true" />
                <span class="line-clamp-1 font-medium text-foreground">{{ job.title }}</span>
            </nav>

            <div class="prose prose-neutral dark:prose-invert max-w-none">
                <p v-if="job.job_title" class="text-sm text-muted-foreground">
                    {{ job.job_title.name }}
                </p>
                <div class="whitespace-pre-wrap text-foreground">
                    {{ job.description }}
                </div>
                <div
                    v-if="job.requirements"
                    class="mt-8 border-t border-border pt-8"
                >
                    <h2 class="text-lg font-semibold">Requirements</h2>
                    <div class="mt-2 whitespace-pre-wrap text-muted-foreground">
                        {{ job.requirements }}
                    </div>
                </div>
                <dl class="mt-8 grid gap-2 text-sm text-muted-foreground">
                    <div v-if="job.salary_from != null || job.salary_to != null">
                        <dt class="inline font-medium text-foreground">Salary:</dt>
                        <dd class="inline">
                            {{ job.salary_from ?? '—' }} –
                            {{ job.salary_to ?? '—' }}
                            {{ job.salary_currency ?? '' }}
                        </dd>
                    </div>
                    <div v-if="locationDisplay">
                        <dt class="inline font-medium text-foreground">Location:</dt>
                        <dd class="inline">{{ locationDisplay }}</dd>
                    </div>
                </dl>
            </div>

            <div class="mt-10 flex flex-wrap gap-3">
                <Button
                    v-if="canApply && !hasApplied"
                    type="button"
                    size="lg"
                    class="gap-2"
                    @click="scrollToParticipation"
                >
                    <UserPlus class="h-4 w-4" aria-hidden="true" />
                    Submit participation
                </Button>
                <Button variant="outline" size="lg" as-child>
                    <Link :href="remoteWorkRoutes.index.url()">
                        Back to all posts
                    </Link>
                </Button>
            </div>

            <div
                id="participation"
                ref="participationSection"
                class="mt-12 scroll-mt-24"
            >
            <Card>
                <CardHeader>
                    <CardTitle>Participation</CardTitle>
                    <CardDescription>
                        Request to join this remote work opportunity. The poster will review your
                        application.
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <p
                        v-if="flash?.success"
                        class="text-sm font-medium text-green-600 dark:text-green-400"
                    >
                        {{ flash.success }}
                    </p>
                    <p
                        v-if="flash?.error"
                        class="text-sm font-medium text-destructive"
                    >
                        {{ flash.error }}
                    </p>
                    <template v-if="hasApplied">
                        <p class="text-sm text-muted-foreground">
                            You have already submitted your participation for this post.
                        </p>
                    </template>
                    <template v-else-if="canApply">
                        <form class="space-y-4" @submit.prevent="submitApplication">
                            <div>
                                <Label for="cover_message">Message (optional)</Label>
                                <Textarea
                                    id="cover_message"
                                    v-model="applyForm.cover_message"
                                    class="mt-1 min-h-[100px]"
                                    placeholder="Introduce yourself and why you are a good fit."
                                />
                                <p
                                    v-if="applyForm.errors.cover_message"
                                    class="mt-1 text-sm text-destructive"
                                >
                                    {{ applyForm.errors.cover_message }}
                                </p>
                            </div>
                            <Button
                                type="submit"
                                size="lg"
                                class="gap-2"
                                :disabled="applyForm.processing"
                            >
                                <UserPlus class="h-4 w-4" aria-hidden="true" />
                                Submit participation
                            </Button>
                        </form>
                    </template>
                    <template v-else-if="!auth?.user">
                        <p class="text-sm text-muted-foreground">
                            <Link :href="login().url" class="font-medium text-primary underline">
                                Sign in
                            </Link>
                            with a verified developer account to participate.
                        </p>
                    </template>
                    <template v-else>
                        <p class="text-sm text-muted-foreground">
                            You need an approved developer profile to participate, and you cannot
                            join your own post.
                        </p>
                    </template>
                </CardContent>
            </Card>
            </div>
        </article>
        <Footer />
    </div>
</template>
