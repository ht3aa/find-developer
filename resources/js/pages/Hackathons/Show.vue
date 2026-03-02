<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Award } from 'lucide-vue-next';
import BadgeIcon from '@/components/BadgeIcon.vue';
import Footer from '@/components/Footer.vue';
import Navbar from '@/components/Navbar.vue';
import SeoHead from '@/components/SeoHead.vue';

export type PublicHackathonDetail = {
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

const props = defineProps<{
    hackathon: PublicHackathonDetail;
}>();

function formatDate(iso: string | null): string {
    if (!iso) return '';
    const d = new Date(iso);
    return d.toLocaleDateString(undefined, { dateStyle: 'long' });
}

const hackathonCanonical = computed(() => `/hackathons/${props.hackathon.slug}`);

/** Extract YouTube video ID from URL for embed. */
function youtubeEmbedUrl(url: string | null): string | null {
    if (!url) return null;
    try {
        const u = new URL(url);
        const v = u.searchParams.get('v') ?? u.pathname.split('/').filter(Boolean).pop();
        return v ? `https://www.youtube.com/embed/${v}` : null;
    } catch {
        return null;
    }
}

const embedUrl = computed(() => youtubeEmbedUrl(props.hackathon.youtube_url));
</script>

<template>
    <SeoHead
        :title="hackathon.title"
        :description="hackathon.body ? hackathon.body.slice(0, 160) : undefined"
        :image="hackathon.image_url"
        :canonical="hackathonCanonical"
    />
    <Head>
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <article class="mx-auto w-full max-w-3xl flex-1 px-4 py-12 sm:px-6 lg:px-8">
            <header class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                    {{ hackathon.title }}
                </h1>
                <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-muted-foreground">
                    <time v-if="hackathon.start_date || hackathon.end_date" :datetime="hackathon.start_date ?? hackathon.end_date ?? undefined">
                        <span v-if="hackathon.start_date && hackathon.end_date">
                            {{ formatDate(hackathon.start_date) }} – {{ formatDate(hackathon.end_date) }}
                        </span>
                        <span v-else>{{ formatDate(hackathon.start_date ?? hackathon.end_date) }}</span>
                    </time>
                </div>
                <time
                    v-if="hackathon.created_at && !hackathon.start_date && !hackathon.end_date"
                    class="mt-2 block text-sm text-muted-foreground"
                    :datetime="hackathon.created_at"
                >
                    {{ formatDate(hackathon.created_at) }}
                </time>
            </header>

            <div
                v-if="hackathon.image_url"
                class="mb-8 aspect-video w-full overflow-hidden rounded-xl bg-muted"
            >
                <img
                    :src="hackathon.image_url"
                    :alt="hackathon.title"
                    class="size-full object-cover"
                />
            </div>

            <div
                v-if="hackathon.body"
                class="prose prose-neutral dark:prose-invert max-w-none mb-8"
                v-html="hackathon.body"
            />

            <div
                v-if="embedUrl"
                class="mb-8 aspect-video w-full overflow-hidden rounded-xl bg-muted"
            >
                <iframe
                    :src="embedUrl"
                    title="YouTube video"
                    class="size-full"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                />
            </div>

            <section
                v-if="hackathon.reward_badge || hackathon.reward_description"
                class="mb-8 rounded-xl border border-border bg-gradient-to-br from-primary/5 to-primary/10 p-6 dark:from-primary/10 dark:to-primary/5"
            >
                <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold tracking-tight">
                    <Award class="size-5 text-primary" aria-hidden="true" />
                    Reward
                </h2>
                <div v-if="hackathon.reward_badge" class="mb-4">
                    <Link
                        :href="`/badges`"
                        class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium transition-colors hover:opacity-90"
                        :class="hackathon.reward_badge.color ? '' : 'bg-primary text-primary-foreground'"
                        :style="hackathon.reward_badge.color
                            ? { backgroundColor: `${hackathon.reward_badge.color}25`, color: hackathon.reward_badge.color, borderWidth: '1px', borderStyle: 'solid', borderColor: `${hackathon.reward_badge.color}40` }
                            : undefined"
                    >
                        <BadgeIcon
                            v-if="hackathon.reward_badge.icon"
                            :icon="hackathon.reward_badge.icon"
                            icon-class="size-4 shrink-0"
                        />
                        <Award
                            v-else
                            class="size-4 shrink-0"
                            aria-hidden="true"
                        />
                        {{ hackathon.reward_badge.name }}
                    </Link>
                </div>
                <p
                    v-if="hackathon.reward_description"
                    class="text-sm text-muted-foreground leading-relaxed"
                >
                    {{ hackathon.reward_description }}
                </p>
            </section>

            <footer class="mt-12 border-t pt-8">
                <Link
                    href="/hackathons"
                    class="text-sm font-medium text-primary hover:underline"
                >
                    ← Back to hackathons
                </Link>
            </footer>
        </article>

        <Footer />
    </div>
</template>
