<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, Briefcase } from 'lucide-vue-next';
import Footer from '@/components/Footer.vue';
import Hero from '@/components/Hero.vue';
import Navbar from '@/components/Navbar.vue';
import Pagination from '@/components/Pagination.vue';
import SeoHead from '@/components/SeoHead.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import remoteWorkRoutes from '@/routes/remote-work';

type JobRow = {
    id: number;
    title: string;
    slug: string;
    description: string;
    company_name: string;
    salary_from: number | null;
    salary_to: number | null;
    salary_currency: string | null;
    job_title?: { name: string } | null;
};

type Paginated = {
    data: JobRow[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
};

defineProps<{
    jobs: Paginated;
}>();
</script>

<template>
    <SeoHead
        title="Remote work"
        description="Browse approved remote work opportunities posted by clients."
        :canonical="remoteWorkRoutes.index.url()"
    />
    <Head title="Remote work" />
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />
        <Hero
            badge="Opportunities"
            title="Remote work"
            description="Browse projects that have been reviewed and approved. Apply with your developer profile."
        />
        <section class="mx-auto w-full max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div
                v-if="jobs.data.length === 0"
                class="rounded-xl border border-dashed border-border py-16 text-center"
            >
                <Briefcase
                    class="mx-auto mb-4 h-12 w-12 text-muted-foreground"
                    aria-hidden="true"
                />
                <h2 class="text-lg font-semibold text-foreground">No posts yet</h2>
                <p class="mt-2 text-sm text-muted-foreground">
                    Approved remote work posts will appear here.
                </p>
            </div>
            <div v-else class="space-y-8">
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Card
                        v-for="job in jobs.data"
                        :key="job.id"
                        class="flex h-full flex-col overflow-hidden transition-shadow hover:shadow-md"
                    >
                        <CardContent class="flex flex-1 flex-col p-5">
                            <p
                                v-if="job.job_title"
                                class="text-xs font-medium uppercase tracking-wide text-muted-foreground"
                            >
                                {{ job.job_title.name }}
                            </p>
                            <h3 class="mt-1 line-clamp-2 font-semibold tracking-tight">
                                {{ job.title }}
                            </h3>
                            <p class="mt-1 text-sm text-muted-foreground">
                                {{ job.company_name }}
                            </p>
                            <p class="mt-3 line-clamp-3 flex-1 text-sm text-muted-foreground">
                                {{ job.description }}
                            </p>
                            <div class="mt-5">
                                <Button class="w-full gap-2 sm:w-auto" variant="default" as-child>
                                    <Link :href="remoteWorkRoutes.show.url(job.slug)">
                                        View details
                                        <ArrowRight class="h-4 w-4" aria-hidden="true" />
                                    </Link>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
                <Pagination :links="jobs.links" />
            </div>
        </section>
        <Footer />
    </div>
</template>
