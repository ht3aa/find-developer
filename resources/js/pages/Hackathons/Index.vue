<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Award, Calendar, Trophy } from 'lucide-vue-next';
import BadgeIcon from '@/components/BadgeIcon.vue';
import Footer from '@/components/Footer.vue';
import Hero from '@/components/Hero.vue';
import Navbar from '@/components/Navbar.vue';
import SeoHead from '@/components/SeoHead.vue';
import { Card, CardContent } from '@/components/ui/card';

export type PublicHackathonEntry = {
    id: number;
    title: string;
    slug: string;
    body: string | null;
    image_url: string | null;
    youtube_url: string | null;
    reward_badge_id: number | null;
    reward_badge: { id: number; name: string; slug: string; icon: string | null; color: string | null } | null;
    reward_description: string | null;
    start_date: string | null;
    end_date: string | null;
    created_at: string | null;
};

defineProps<{
    hackathons: PublicHackathonEntry[];
}>();

function formatDate(iso: string | null): string {
    if (!iso) return '';
    const d = new Date(iso);
    return d.toLocaleDateString(undefined, { dateStyle: 'medium' });
}

function dateRange(start: string | null, end: string | null): string {
    if (!start && !end) return '';
    if (start && end) return `${formatDate(start)} – ${formatDate(end)}`;
    return start ? formatDate(start) : formatDate(end);
}
</script>

<template>
    <SeoHead
        title="Hackathons"
        description="Discover hackathons we've participated in or supported. Events, projects, and outcomes."
        canonical="/hackathons"
    />
    <Head>
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <Hero
            badge="Hackathons"
            title="Hackathons"
            description="Discover hackathons we've participated in or supported. Events, projects, and outcomes."
        />

        <section class="mx-auto w-full max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div
                v-if="hackathons.length === 0"
                class="rounded-xl border border-dashed border-border py-16 text-center"
            >
                <Trophy
                    class="mx-auto mb-4 h-12 w-12 text-muted-foreground"
                    aria-hidden="true"
                />
                <h2 class="text-lg font-semibold text-foreground">
                    No hackathons yet
                </h2>
                <p class="mt-2 text-sm text-muted-foreground">
                    Hackathons will appear here once they are added.
                </p>
            </div>
            <div
                v-else
                class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
            >
                <Link
                    v-for="hackathon in hackathons"
                    :key="hackathon.id"
                    :href="`/hackathons/${hackathon.slug}`"
                    class="group block transition-all duration-200 hover:-translate-y-1"
                >
                    <Card class="h-full overflow-hidden border-border/80 transition-all duration-200 group-hover:border-primary/30 group-hover:shadow-lg">
                        <div
                            v-if="hackathon.image_url"
                            class="relative aspect-video w-full shrink-0 overflow-hidden bg-muted"
                        >
                            <img
                                :src="hackathon.image_url"
                                :alt="hackathon.title"
                                class="size-full object-cover transition-transform duration-300 group-hover:scale-105"
                            />
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 transition-opacity duration-200 group-hover:opacity-100"
                                aria-hidden="true"
                            />
                        </div>
                        <CardContent class="flex flex-col p-5">
                            <h3 class="font-semibold tracking-tight text-foreground line-clamp-2 group-hover:text-primary">
                                {{ hackathon.title }}
                            </h3>
                            <p
                                v-if="hackathon.body"
                                class="mt-2 line-clamp-3 text-sm leading-relaxed text-muted-foreground"
                            >
                                {{ hackathon.body }}
                            </p>
                            <div
                                v-if="hackathon.reward_badge || hackathon.reward_description"
                                class="mt-4 rounded-lg border border-border/60 bg-muted/40 px-3 py-2.5"
                            >
                                <div class="mb-1.5 flex items-center gap-1.5 text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                    <Award class="size-3.5 shrink-0 text-primary" aria-hidden="true" />
                                    Reward
                                </div>
                                <div
                                    v-if="hackathon.reward_badge"
                                    class="inline-flex w-fit items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium shadow-sm"
                                    :class="hackathon.reward_badge.color ? '' : 'bg-primary/15 text-primary'"
                                    :style="hackathon.reward_badge.color
                                        ? {
                                            backgroundColor: `${hackathon.reward_badge.color}22`,
                                            color: hackathon.reward_badge.color,
                                            boxShadow: `0 0 0 1px ${hackathon.reward_badge.color}40`,
                                        }
                                        : undefined"
                                >
                                    <BadgeIcon
                                        v-if="hackathon.reward_badge.icon"
                                        :icon="hackathon.reward_badge.icon"
                                        icon-class="size-3.5 shrink-0"
                                    />
                                    <Award
                                        v-else
                                        class="size-3.5 shrink-0"
                                        aria-hidden="true"
                                    />
                                    {{ hackathon.reward_badge.name }}
                                </div>
                                <p
                                    v-if="hackathon.reward_description"
                                    class="mt-2 line-clamp-2 text-xs leading-relaxed text-muted-foreground"
                                >
                                    {{ hackathon.reward_description }}
                                </p>
                            </div>
                            <div class="mt-4 flex items-center gap-1.5 text-xs text-muted-foreground">
                                <Calendar class="size-3.5 shrink-0" aria-hidden="true" />
                                <template v-if="hackathon.start_date || hackathon.end_date">
                                    {{ dateRange(hackathon.start_date, hackathon.end_date) }}
                                </template>
                                <template v-else>
                                    {{ formatDate(hackathon.created_at) }}
                                </template>
                            </div>
                        </CardContent>
                    </Card>
                </Link>
            </div>
        </section>

        <Footer />
    </div>
</template>
