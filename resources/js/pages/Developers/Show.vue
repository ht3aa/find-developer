<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Briefcase,
    FileText,
    Globe,
    Mail,
    MapPin,
    Phone,
    Star,
    ThumbsUp,
    Video,
} from 'lucide-vue-next';
import { computed } from 'vue';
import Footer from '@/components/Footer.vue';
import Navbar from '@/components/Navbar.vue';
import BadgeIcon from '@/components/BadgeIcon.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { home, login } from '@/routes';
import type { Developer } from '@/types/developer';

const props = defineProps<{
    developer: Developer;
}>();

const page = usePage();
const auth = computed(() => page.props.auth as { user?: { is_admin?: boolean } } | undefined);
const isAdmin = computed(() => auth.value?.user?.is_admin === true);
const isGuest = computed(() => !auth.value?.user);

const showSalary = computed(() => {
    if (!props.developer.expected_salary_from && !props.developer.expected_salary_to) return false;
    return isAdmin.value;
});

const salaryLabel = computed(() => {
    const d = props.developer;
    const from = d.expected_salary_from;
    const to = d.expected_salary_to;
    const cur = d.currency ?? '';
    if (from && to) return `${formatNum(from)} - ${formatNum(to)} ${cur}/month`;
    if (from) return `From ${formatNum(from)} ${cur}/year`;
    if (to) return `Up to ${formatNum(to)} ${cur}/year`;
    return '';
});

const subscribeToSeeSalaryUrl = computed(() => {
    return `mailto:contact@example.com?subject=Subscription%20Inquiry`;
});

const experienceLabel = computed(() => {
    const n = props.developer.years_of_experience;
    return n === 1 ? '1 Year Experience' : `${n} Years Experience`;
});

function formatNum(n: number): string {
    return new Intl.NumberFormat().format(n);
}
</script>

<template>
    <Head :title="`${developer.name} | Find Developer`" />
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <main class="flex-1">
            <!-- Back link -->
            <div class="border-b border-border bg-muted/30">
                <div class="mx-auto max-w-6xl px-4 py-3">
                    <Link
                        :href="home().url"
                        class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                    >
                        <ArrowLeft class="size-4" />
                        Back to Search
                    </Link>
                </div>
            </div>

            <div class="mx-auto max-w-6xl px-4 py-6 sm:py-8">
                <!-- Hero: name + job title + badges -->
                <header class="mb-6">
                    <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        {{ developer.name }}
                    </h1>
                    <p class="mt-1 text-lg font-medium text-muted-foreground">
                        {{ developer.job_title?.name }}
                    </p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <TooltipProvider
                            v-for="badge in developer.badges"
                            :key="badge.name"
                        >
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <span
                                        class="inline-flex size-9 items-center justify-center rounded-lg border transition-colors"
                                        :class="badge.color ? '' : 'border-border bg-muted'"
                                        :style="
                                            badge.color
                                                ? {
                                                      background: `${badge.color}18`,
                                                      borderColor: `${badge.color}50`,
                                                      color: badge.color,
                                                  }
                                                : {}
                                        "
                                    >
                                        <BadgeIcon v-if="badge.icon" :icon="badge.icon" />
                                    </span>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{{ badge.name }}</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </div>
                </header>

                <!-- Stats bar: experience, location, status -->
                <div class="mb-8 flex flex-wrap items-center gap-x-6 gap-y-2 border-b border-border pb-6 text-sm">
                    <span class="flex items-center gap-2 font-medium text-foreground">
                        <Briefcase class="size-4 text-muted-foreground" />
                        {{ experienceLabel }}
                    </span>
                    <span
                        v-if="developer.location"
                        class="flex items-center gap-2 text-muted-foreground"
                    >
                        <MapPin class="size-4 shrink-0" />
                        {{ developer.location.label }} Location
                    </span>
                    <span
                        class="inline-flex items-center gap-1.5 font-medium"
                        :class="
                            developer.is_available
                                ? 'text-availability-available'
                                : 'text-availability-unavailable'
                        "
                    >
                        <span
                            class="size-2 rounded-full"
                            :class="
                                developer.is_available
                                    ? 'bg-availability-available animate-pulse'
                                    : 'bg-availability-unavailable'
                            "
                        />
                        {{ developer.is_available ? 'Available' : 'Not Available' }} Status
                    </span>
                    <span
                        v-if="developer.recommendations_received_count > 0"
                        class="flex items-center gap-2 text-muted-foreground"
                    >
                        <ThumbsUp class="size-4" />
                        {{ developer.recommendations_received_count }}
                        {{ developer.recommendations_received_count === 1 ? 'Recommendation' : 'Recommendations' }}
                    </span>
                </div>

                <!-- Recommended pill -->
                <div
                    v-if="developer.recommended_by_us"
                    class="mb-8 flex w-fit items-center gap-1.5 rounded-full border border-primary/40 bg-primary/15 px-3 py-1.5 text-xs font-semibold text-primary shadow-sm"
                >
                    <Star class="size-3.5 shrink-0" />
                    Recommended
                </div>

                <div class="grid gap-8 lg:grid-cols-[1fr_320px]">
                    <!-- Main column -->
                    <article class="min-w-0 space-y-8">
                        <!-- About -->
                        <section v-if="developer.bio">
                            <h2 class="mb-3 text-lg font-semibold">
                                About
                            </h2>
                            <p class="whitespace-pre-wrap text-muted-foreground leading-relaxed">
                                {{ developer.bio }}
                            </p>
                        </section>

                        <!-- Skills & Technologies -->
                        <section v-if="developer.skills.length > 0">
                            <h2 class="mb-3 text-lg font-semibold">
                                Skills & Technologies
                            </h2>
                            <div class="flex flex-wrap gap-2">
                                <Badge
                                    v-for="skill in developer.skills"
                                    :key="skill.name"
                                    variant="secondary"
                                    class="rounded-md px-3 py-1 text-sm font-normal"
                                >
                                    {{ skill.name }}
                                </Badge>
                            </div>
                        </section>

                        <!-- Experience video -->
                        <section v-if="developer.youtube_video_id">
                            <h2 class="mb-3 text-lg font-semibold">
                                Experience Video
                            </h2>
                            <div class="overflow-hidden rounded-xl border border-border bg-muted">
                                <div class="aspect-video w-full">
                                    <iframe
                                        :src="`https://www.youtube.com/embed/${developer.youtube_video_id}?autoplay=0&mute=1`"
                                        title="Experience video"
                                        class="size-full"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen
                                    />
                                </div>
                            </div>
                        </section>

                        <!-- Get In Touch -->
                        <section>
                            <h2 class="mb-4 text-lg font-semibold">
                                Get In Touch
                            </h2>
                            <div class="flex flex-wrap gap-3">
                                <Button
                                    variant="outline"
                                    size="default"
                                    as-child
                                    class="gap-2 rounded-lg"
                                >
                                    <a :href="`mailto:${developer.email}`">
                                        <Mail class="size-4" />
                                        Send Email
                                    </a>
                                </Button>
                                <Button
                                    v-if="developer.github_url"
                                    variant="outline"
                                    size="default"
                                    as-child
                                    class="gap-2 rounded-lg"
                                >
                                    <a
                                        :href="developer.github_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <svg class="size-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                                        </svg>
                                        GitHub Profile
                                    </a>
                                </Button>
                                <Button
                                    v-if="developer.linkedin_url"
                                    variant="outline"
                                    size="default"
                                    as-child
                                    class="gap-2 rounded-lg"
                                >
                                    <a
                                        :href="developer.linkedin_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <svg class="size-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                                        </svg>
                                        LinkedIn
                                    </a>
                                </Button>
                                <Button
                                    v-if="developer.youtube_video_id"
                                    variant="outline"
                                    size="default"
                                    as-child
                                    class="gap-2 rounded-lg"
                                >
                                    <a
                                        :href="`https://www.youtube.com/watch?v=${developer.youtube_video_id}`"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <Video class="size-4" />
                                        Experience Video
                                    </a>
                                </Button>
                                <Button
                                    v-if="developer.portfolio_url"
                                    variant="outline"
                                    size="default"
                                    as-child
                                    class="gap-2 rounded-lg"
                                >
                                    <a
                                        :href="developer.portfolio_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <Globe class="size-4" />
                                        Portfolio
                                    </a>
                                </Button>
                            </div>
                        </section>

                        <!-- CV / Resume -->
                        <section v-if="developer.cv_path_url">
                            <h2 class="mb-3 text-lg font-semibold">
                                CV / Resume
                            </h2>
                            <Button
                                variant="outline"
                                size="default"
                                as-child
                                class="gap-2 rounded-lg"
                            >
                                <a
                                    :href="developer.cv_path_url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <FileText class="size-4" />
                                    View CV / Resume
                                </a>
                            </Button>
                        </section>

                        <!-- Recommendations -->
                        <section>
                            <h2 class="mb-3 text-lg font-semibold">
                                Recommendations ({{ developer.recommendations_received_count }})
                            </h2>
                            <p
                                v-if="isGuest"
                                class="text-sm text-muted-foreground"
                            >
                                <Link
                                    :href="login().url"
                                    class="font-medium text-primary hover:underline"
                                >
                                    Login to Recommend
                                </Link>
                            </p>
                            <p
                                v-else-if="developer.recommendations_received_count === 0"
                                class="text-sm text-muted-foreground"
                            >
                                No recommendations yet.
                            </p>
                            <p
                                v-else
                                class="text-sm text-muted-foreground"
                            >
                                {{ developer.recommendations_received_count }}
                                {{ developer.recommendations_received_count === 1 ? 'recommendation' : 'recommendations' }}
                                received.
                            </p>
                        </section>
                    </article>

                    <!-- Sidebar: Quick Info -->
                    <aside>
                        <div class="sticky top-24 rounded-xl border border-border bg-card p-5 shadow-sm">
                            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-muted-foreground">
                                Quick Info
                            </h2>
                            <dl class="space-y-3 text-sm">
                                <div>
                                    <dt class="font-medium text-muted-foreground">Role</dt>
                                    <dd class="mt-0.5 font-medium text-foreground">
                                        {{ developer.job_title?.name }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-muted-foreground">Experience</dt>
                                    <dd class="mt-0.5 font-medium text-foreground">
                                        {{ experienceLabel }}
                                    </dd>
                                </div>
                                <div v-if="developer.location">
                                    <dt class="font-medium text-muted-foreground">Location</dt>
                                    <dd class="mt-0.5 font-medium text-foreground">
                                        {{ developer.location.label }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-muted-foreground">Availability</dt>
                                    <dd class="mt-0.5 font-medium">
                                        <span
                                            :class="
                                                developer.is_available
                                                    ? 'text-availability-available'
                                                    : 'text-availability-unavailable'
                                            "
                                        >
                                            {{ developer.is_available ? 'Available' : 'Not Available' }}
                                        </span>
                                    </dd>
                                </div>
                                <div v-if="developer.phone">
                                    <dt class="font-medium text-muted-foreground">Phone</dt>
                                    <dd class="mt-0.5">
                                        <a
                                            :href="`tel:${developer.phone}`"
                                            class="font-medium text-primary hover:underline"
                                        >
                                            {{ developer.phone }}
                                        </a>
                                    </dd>
                                </div>
                                <div
                                    v-if="developer.expected_salary_from || developer.expected_salary_to"
                                >
                                    <dt class="font-medium text-muted-foreground">Salary</dt>
                                    <dd class="mt-0.5">
                                        <template v-if="showSalary">
                                            <span class="font-medium text-foreground">{{ salaryLabel }}</span>
                                        </template>
                                        <a
                                            v-else
                                            :href="subscribeToSeeSalaryUrl"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="font-medium text-primary hover:underline"
                                        >
                                            Subscribe to see salary
                                        </a>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </aside>
                </div>
            </div>
        </main>

        <Footer />
    </div>
</template>
