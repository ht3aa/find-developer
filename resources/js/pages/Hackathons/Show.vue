<script setup lang="ts">
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { Award, CheckCircle2, ChevronDown, UserPlus } from 'lucide-vue-next';
import { computed } from 'vue';
import BadgeIcon from '@/components/BadgeIcon.vue';
import Footer from '@/components/Footer.vue';
import InputError from '@/components/InputError.vue';
import Navbar from '@/components/Navbar.vue';
import SeoHead from '@/components/SeoHead.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { login } from '@/routes';

export type PublicHackathonDetail = {
    id: number;
    title: string;
    slug: string;
    body: string | null;
    image_url: string | null;
    youtube_url: string | null;
    youtube_video_id: string | null;
    reward_badge_id: number | null;
    reward_badge: {
        id: number;
        name: string;
        slug: string;
        icon: string | null;
        color: string | null;
    } | null;
    reward_description: string | null;
    start_date: string | null;
    end_date: string | null;
    created_at: string | null;
    has_teams?: boolean;
    teams_url?: string | null;
};

const props = defineProps<{
    hackathon: PublicHackathonDetail;
    canSubscribe: boolean;
    alreadySubscribed: boolean;
    subscribersCount: number;
    subscribeUrl: string;
    /** URL to view subscribed developers (null when none confirmed). */
    subscribersUrl?: string | null;
}>();

const page = usePage();
const flash = computed(
    () =>
        page.props.flash as
            | { success?: string; error?: string; info?: string }
            | undefined,
);
const auth = computed(() => page.props.auth as { user: unknown } | undefined);
const isGuest = computed(() => !auth.value?.user);

function formatDate(iso: string | null): string {
    if (!iso) return '';
    const d = new Date(iso);
    return d.toLocaleDateString(undefined, { dateStyle: 'long' });
}

const hackathonCanonical = computed(
    () => `/hackathons/${props.hackathon.slug}`,
);

function scrollToRegister(): void {
    document
        .getElementById('hackathon-register')
        ?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
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

        <article
            class="mx-auto w-full max-w-3xl flex-1 px-4 py-12 sm:px-6 lg:px-8"
        >
            <div
                v-if="flash?.success"
                class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:border-green-800 dark:bg-green-950/50 dark:text-green-200"
            >
                {{ flash.success }}
            </div>
            <div
                v-if="flash?.error"
                class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 dark:border-red-800 dark:bg-red-950/50 dark:text-red-200"
            >
                {{ flash.error }}
            </div>
            <div
                v-if="flash?.info"
                class="mb-6 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm font-medium text-blue-800 dark:border-blue-800 dark:bg-blue-950/50 dark:text-blue-200"
            >
                {{ flash.info }}
            </div>

            <header class="mb-8">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1
                            class="text-3xl font-bold tracking-tight sm:text-4xl"
                        >
                            {{ hackathon.title }}
                        </h1>
                        <div
                            class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-muted-foreground"
                        >
                            <time
                                v-if="
                                    hackathon.start_date || hackathon.end_date
                                "
                                :datetime="
                                    hackathon.start_date ??
                                    hackathon.end_date ??
                                    undefined
                                "
                            >
                                <span
                                    v-if="
                                        hackathon.start_date &&
                                        hackathon.end_date
                                    "
                                >
                                    {{ formatDate(hackathon.start_date) }} –
                                    {{ formatDate(hackathon.end_date) }}
                                </span>
                                <span v-else>{{
                                    formatDate(
                                        hackathon.start_date ??
                                            hackathon.end_date,
                                    )
                                }}</span>
                            </time>
                        </div>
                        <time
                            v-if="
                                hackathon.created_at &&
                                !hackathon.start_date &&
                                !hackathon.end_date
                            "
                            class="mt-2 block text-sm text-muted-foreground"
                            :datetime="hackathon.created_at"
                        >
                            {{ formatDate(hackathon.created_at) }}
                        </time>
                    </div>
                    <div
                        class="flex flex-col items-end gap-2 sm:flex-row sm:items-center sm:gap-3"
                    >
                        <Button
                            v-if="canSubscribe || isGuest"
                            type="button"
                            variant="outline"
                            class="shrink-0"
                            @click="scrollToRegister"
                        >
                            <UserPlus
                                class="mr-2 size-4 shrink-0"
                                aria-hidden="true"
                            />
                            Register
                            <ChevronDown
                                class="ml-2 size-4 shrink-0"
                                aria-hidden="true"
                            />
                        </Button>
                        <Button
                            v-if="subscribersUrl"
                            as-child
                            variant="outline"
                            class="shrink-0"
                        >
                            <Link :href="subscribersUrl">
                                View subscribers
                            </Link>
                        </Button>
                        <Button
                            v-if="hackathon.has_teams && hackathon.teams_url"
                            as-child
                            variant="outline"
                            class="shrink-0"
                        >
                            <Link :href="hackathon.teams_url">
                                View teams
                            </Link>
                        </Button>
                    </div>
                </div>
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
                class="prose prose-neutral dark:prose-invert mb-8 max-w-none"
                v-html="hackathon.body"
            />

            <div
                v-if="hackathon.youtube_video_id"
                class="mb-8 aspect-video w-full overflow-hidden rounded-xl bg-muted"
            >
                <iframe
                    :src="`https://www.youtube.com/embed/${hackathon.youtube_video_id}`"
                    title="YouTube video"
                    class="size-full"
                    allow="
                        accelerometer;
                        autoplay;
                        clipboard-write;
                        encrypted-media;
                        gyroscope;
                        picture-in-picture;
                    "
                    allowfullscreen
                />
            </div>

            <section
                v-if="hackathon.reward_badge || hackathon.reward_description"
                class="mb-8 rounded-xl border border-border bg-gradient-to-br from-primary/5 to-primary/10 p-6 dark:from-primary/10 dark:to-primary/5"
            >
                <h2
                    class="mb-4 flex items-center gap-2 text-lg font-semibold tracking-tight"
                >
                    <Award class="size-5 text-primary" aria-hidden="true" />
                    Reward
                </h2>
                <div v-if="hackathon.reward_badge" class="mb-4">
                    <Link
                        :href="`/badges`"
                        class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium transition-colors hover:opacity-90"
                        :class="
                            hackathon.reward_badge.color
                                ? ''
                                : 'bg-primary text-primary-foreground'
                        "
                        :style="
                            hackathon.reward_badge.color
                                ? {
                                      backgroundColor: `${hackathon.reward_badge.color}25`,
                                      color: hackathon.reward_badge.color,
                                      borderWidth: '1px',
                                      borderStyle: 'solid',
                                      borderColor: `${hackathon.reward_badge.color}40`,
                                  }
                                : undefined
                        "
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
                    class="text-sm leading-relaxed text-muted-foreground"
                >
                    {{ hackathon.reward_description }}
                </p>
            </section>

            <section
                id="hackathon-register"
                v-if="canSubscribe || subscribersCount > 0 || isGuest"
                class="mb-8 scroll-mt-8 rounded-xl border border-border bg-muted/30 p-6"
            >
                <div
                    class="mb-3 flex items-center gap-2 text-sm text-muted-foreground"
                >
                    <UserPlus class="size-4 shrink-0" aria-hidden="true" />
                    <span
                        >{{ subscribersCount }}
                        {{
                            subscribersCount === 1 ? 'developer' : 'developers'
                        }}
                        registered</span
                    >
                </div>
                <div
                    v-if="isGuest"
                    class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-800 dark:bg-amber-950/50 dark:text-amber-200"
                >
                    <p class="font-medium">
                        Please log in to register for this hackathon.
                    </p>
                    <Link
                        :href="login.url()"
                        class="mt-2 inline-block font-medium text-amber-700 underline hover:no-underline dark:text-amber-300"
                    >
                        Log in
                    </Link>
                </div>
                <div
                    v-else-if="canSubscribe && alreadySubscribed"
                    class="flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:border-green-800 dark:bg-green-950/50 dark:text-green-200"
                >
                    <CheckCircle2 class="size-5 shrink-0" aria-hidden="true" />
                    You're registered for this hackathon. You will receive a
                    confirmation email once it's approved.
                </div>
                <Form
                    v-else-if="canSubscribe && !alreadySubscribed"
                    :action="subscribeUrl"
                    method="post"
                    class="space-y-3"
                    v-slot="{ errors, processing }"
                >
                    <div class="grid gap-2">
                        <Label for="message"> Confirm your attendance </Label>
                        <p class="text-xs text-muted-foreground">
                            Add a short message to confirm you will attend this
                            hackathon.
                        </p>
                        <textarea
                            id="message"
                            name="message"
                            required
                            rows="3"
                            placeholder="e.g. I'm excited to join and looking forward to the event!"
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        />
                        <InputError :message="errors.message" />
                    </div>
                    <Button type="submit" :disabled="processing">
                        Register for this hackathon
                    </Button>
                </Form>
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
