<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Briefcase,
    Building2,
    ExternalLink,
    FileText,
    FolderOpen,
    Globe,
    Mail,
    MapPin,
    Phone,
    Star,
    ThumbsUp,
    Video,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
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

const expandedDescriptions = ref<Set<string>>(new Set());
const DESCRIPTION_TRUNCATE_LENGTH = 200;

function toggleDescription(entryIdx: number, roleIdx: number): void {
    const key = `${entryIdx}-${roleIdx}`;
    const next = new Set(expandedDescriptions.value);
    if (next.has(key)) {
        next.delete(key);
    } else {
        next.add(key);
    }
    expandedDescriptions.value = next;
}

function isExpanded(entryIdx: number, roleIdx: number): boolean {
    return expandedDescriptions.value.has(`${entryIdx}-${roleIdx}`);
}

function shouldTruncate(description: string | null | undefined): boolean {
    return !!description && description.length > DESCRIPTION_TRUNCATE_LENGTH;
}

function truncatedText(text: string): string {
    return text.slice(0, DESCRIPTION_TRUNCATE_LENGTH).trim() + '…';
}
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
    if (from && to) return `${formatNum(from)} – ${formatNum(to)} ${cur}/mo`;
    if (from) return `From ${formatNum(from)} ${cur}/yr`;
    if (to) return `Up to ${formatNum(to)} ${cur}/yr`;
    return '';
});

const subscribeToSeeSalaryUrl = computed(() => {
    return `mailto:contact@example.com?subject=Subscription%20Inquiry`;
});

const experienceLabel = computed(() => {
    const n = props.developer.years_of_experience;
    return n === 1 ? '1 Year' : `${n} Years`;
});

const initials = computed(() =>
    props.developer.name
        .split(' ')
        .map((n: string) => n[0])
        .join(''),
);

function formatNum(n: number): string {
    return new Intl.NumberFormat().format(n);
}
</script>

<template>
    <Head :title="`${developer.name} | Find Developer`" />
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <main class="flex-1">
            <!-- Back bar -->
            <div
                class="sticky top-0 z-10 border-b border-border bg-card/60 backdrop-blur-sm"
            >
                <div class="mx-auto max-w-5xl px-4 py-3">
                    <Link
                        :href="home()"
                        class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                    >
                        <ArrowLeft class="size-4" />
                        Back to Search
                    </Link>
                </div>
            </div>

            <div class="mx-auto max-w-5xl px-4 py-8 sm:py-12">
                <!-- Hero -->
                <header class="mb-8">
                    <div class="flex items-start gap-5">
                        <!-- Avatar -->
                        <div
                            class="hidden size-20 shrink-0 items-center justify-center rounded-2xl bg-primary/10 text-2xl font-bold text-primary sm:flex"
                        >
                            {{ initials }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-3">
                                <h1
                                    class="text-3xl font-extrabold tracking-tight sm:text-4xl"
                                >
                                    {{ developer.name }}
                                </h1>
                                <span
                                    v-if="developer.recommended_by_us"
                                    class="inline-flex items-center gap-1.5 rounded-full border border-primary/20 bg-primary/10 px-3 py-1 text-xs font-semibold text-primary"
                                >
                                    <Star class="size-3.5 shrink-0" />
                                    Recommended
                                </span>
                            </div>
                            <p
                                class="mt-1 text-lg font-medium text-muted-foreground"
                            >
                                {{ developer.job_title?.name }}
                            </p>

                            <!-- Badges -->
                            <TooltipProvider>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <Tooltip
                                        v-for="badge in developer.badges"
                                        :key="badge.name"
                                    >
                                        <TooltipTrigger as-child>
                                            <span
                                                class="inline-flex size-9 items-center justify-center rounded-xl border border-border bg-muted transition-all hover:scale-110"
                                                :style="
                                                    badge.color
                                                        ? {
                                                              background: `${badge.color}15`,
                                                              borderColor: `${badge.color}40`,
                                                              color: badge.color,
                                                          }
                                                        : undefined
                                                "
                                            >
                                                <BadgeIcon
                                                    v-if="badge.icon"
                                                    :icon="badge.icon"
                                                />
                                            </span>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{{ badge.name }}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </div>
                            </TooltipProvider>
                        </div>
                    </div>

                    <!-- Stats strip -->
                    <div
                        class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-2 rounded-xl border border-border bg-card px-5 py-3.5 text-sm shadow-sm"
                    >
                        <span class="flex items-center gap-2 font-medium">
                            <Briefcase
                                class="size-4 text-muted-foreground"
                            />
                            {{ experienceLabel }} Experience
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
                </header>


                <!-- Content grid -->
                <div class="grid gap-8 lg:grid-cols-[1fr_300px]">
                    <!-- Main -->
                    <div class="min-w-0 space-y-8">
                        <!-- Experience video -->
                        <section v-if="developer.youtube_video_id">
                            <h2 class="mb-3 text-lg font-semibold">
                                Experience Video
                            </h2>
                            <div
                                class="overflow-hidden rounded-xl border border-border shadow-sm"
                            >
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


                        <!-- About -->
                        <section v-if="developer.bio">
                            <h2 class="mb-3 text-lg font-semibold">
                                About
                            </h2>
                            <p
                                class="whitespace-pre-wrap leading-relaxed text-muted-foreground"
                            >
                                {{ developer.bio }}
                            </p>
                        </section>
                        <!-- Get In Touch -->
                        <section>
                            <h2 class="mb-4 text-lg font-semibold">
                                Get In Touch
                            </h2>
                            <div class="flex flex-wrap gap-3">
                                <Button
                                    variant="outline"
                                    as-child
                                    class="gap-2 rounded-xl"
                                >
                                    <a :href="`mailto:${developer.email}`">
                                        <Mail class="size-4" />
                                        Send Email
                                    </a>
                                </Button>
                                <Button
                                    v-if="developer.github_url"
                                    variant="outline"
                                    as-child
                                    class="gap-2 rounded-xl"
                                >
                                    <a
                                        :href="developer.github_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <svg
                                            class="size-4"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                        GitHub Profile
                                    </a>
                                </Button>
                                <Button
                                    v-if="developer.linkedin_url"
                                    variant="outline"
                                    as-child
                                    class="gap-2 rounded-xl"
                                >
                                    <a
                                        :href="developer.linkedin_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <svg
                                            class="size-4"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"
                                            />
                                        </svg>
                                        LinkedIn
                                    </a>
                                </Button>
                                <Button
                                    v-if="developer.youtube_video_id"
                                    variant="outline"
                                    as-child
                                    class="gap-2 rounded-xl"
                                >
                                    <a
                                        :href="`https://www.youtube.com/watch?v=${developer.youtube_video_id}`"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <Video class="size-4" />
                                        Video
                                    </a>
                                </Button>
                                <Button
                                    v-if="developer.portfolio_url"
                                    variant="outline"
                                    as-child
                                    class="gap-2 rounded-xl"
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
                                    class="rounded-lg px-3 py-1.5 text-sm font-medium"
                                >
                                    {{ skill.name }}
                                </Badge>
                            </div>
                        </section>

                        <!-- Work Experience -->
                        <section v-if="developer.work_experience && developer.work_experience.length > 0">
                            <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold">
                                <Building2 class="size-5 shrink-0 text-muted-foreground" />
                                Work Experience
                            </h2>
                            <div class="space-y-6">
                                <div
                                    v-for="(entry, entryIdx) in developer.work_experience"
                                    :key="entryIdx"
                                    class="rounded-xl border border-border bg-card p-5 shadow-sm"
                                >
                                    <!-- Company header -->
                                    <div class="mb-4 flex items-start gap-4">
                                        <div class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-muted/60 text-muted-foreground">
                                            <Building2 class="size-6" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h3 class="text-base font-semibold text-foreground">
                                                {{ entry.company_name }}
                                            </h3>
                                            <span
                                                v-if="entry.total_duration"
                                                class="mt-1 block text-sm text-muted-foreground"
                                            >
                                                {{ entry.total_duration }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Multiple roles timeline -->
                                    <div
                                        v-if="entry.roles.length > 1"
                                        class="space-y-0"
                                    >
                                        <div
                                            v-for="(role, roleIdx) in entry.roles"
                                            :key="roleIdx"
                                            class="relative flex gap-4"
                                        >
                                            <div class="flex min-w-0 flex-col items-center pt-0.5">
                                                <span class="size-2 shrink-0 rounded-full bg-primary/70" />
                                                <span
                                                    v-if="roleIdx < entry.roles.length - 1"
                                                    class="mt-1 min-h-6 w-px flex-1 bg-border"
                                                />
                                            </div>
                                            <div class="min-w-0 flex-1 pb-6 last:pb-0">
                                                <div class="font-medium text-foreground">
                                                    {{ role.job_title || '—' }}
                                                </div>
                                                <div class="mt-1 flex flex-wrap items-center gap-x-2 text-sm text-muted-foreground">
                                                    <span v-if="role.start_date">
                                                        {{ role.start_date }}
                                                        —
                                                        <span
                                                            v-if="role.is_current"
                                                            class="font-medium text-primary"
                                                        >
                                                            Present
                                                        </span>
                                                        <template v-else>
                                                            {{ role.end_date || '—' }}
                                                        </template>
                                                    </span>
                                                    <span class="text-muted-foreground/70">·</span>
                                                    <span>{{ role.duration || '< 1 mo' }}</span>
                                                </div>
                                                <div
                                                    v-if="role.description"
                                                    class="mt-2"
                                                >
                                                    <p
                                                        class="whitespace-pre-wrap text-sm leading-relaxed text-muted-foreground"
                                                    >
                                                        {{ isExpanded(entryIdx, roleIdx) || !shouldTruncate(role.description) ? role.description : truncatedText(role.description) }}
                                                    </p>
                                                    <button
                                                        v-if="shouldTruncate(role.description)"
                                                        type="button"
                                                        class="mt-1 text-sm font-medium text-primary hover:underline"
                                                        :aria-expanded="isExpanded(entryIdx, roleIdx)"
                                                        @click="toggleDescription(entryIdx, roleIdx)"
                                                    >
                                                        {{ isExpanded(entryIdx, roleIdx) ? 'Read less' : 'Read more' }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Single role -->
                                    <div v-else-if="entry.roles.length === 1">
                                        <div
                                            v-for="(role, roleIdx) in entry.roles"
                                            :key="roleIdx"
                                        >
                                            <div class="font-medium text-foreground">
                                                {{ role.job_title || '—' }}
                                            </div>
                                            <div class="mt-1 flex flex-wrap items-center gap-x-2 text-sm text-muted-foreground">
                                                <span v-if="role.start_date">
                                                    {{ role.start_date }}
                                                    —
                                                    <span
                                                        v-if="role.is_current"
                                                        class="font-medium text-primary"
                                                    >
                                                        Present
                                                    </span>
                                                    <template v-else>
                                                        {{ role.end_date || '—' }}
                                                    </template>
                                                </span>
                                                <span class="text-muted-foreground/70">·</span>
                                                <span>{{ role.duration || '< 1 mo' }}</span>
                                            </div>
                                            <div
                                                v-if="role.description"
                                                class="mt-2"
                                            >
                                                <p
                                                    class="whitespace-pre-wrap text-sm leading-relaxed text-muted-foreground"
                                                >
                                                    {{ isExpanded(entryIdx, roleIdx) || !shouldTruncate(role.description) ? role.description : truncatedText(role.description) }}
                                                </p>
                                                <button
                                                    v-if="shouldTruncate(role.description)"
                                                    type="button"
                                                    class="mt-1 text-sm font-medium text-primary hover:underline"
                                                    :aria-expanded="isExpanded(entryIdx, roleIdx)"
                                                    @click="toggleDescription(entryIdx, roleIdx)"
                                                >
                                                    {{ isExpanded(entryIdx, roleIdx) ? 'Read less' : 'Read more' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Portfolio & Projects -->
                        <section v-if="developer.projects && developer.projects.length > 0">
                            <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold">
                                <FolderOpen class="size-5 shrink-0 text-muted-foreground" />
                                Portfolio & Projects
                            </h2>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div
                                    v-for="(project, idx) in developer.projects"
                                    :key="idx"
                                    class="rounded-xl border border-border bg-card p-5 shadow-sm"
                                >
                                    <h3 class="font-semibold text-foreground">
                                        {{ project.title }}
                                    </h3>
                                    <p
                                        v-if="project.description"
                                        class="mt-2 text-sm leading-relaxed text-muted-foreground"
                                    >
                                        {{ project.description }}
                                    </p>
                                    <a
                                        v-if="project.link"
                                        :href="project.link"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="mt-3 inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline"
                                    >
                                        View Project
                                        <ExternalLink class="size-4" />
                                    </a>
                                </div>
                            </div>
                        </section>






                        <!-- Recommendations -->
                        <section>
                            <h2 class="mb-3 text-lg font-semibold">
                                Recommendations ({{ developer.recommendations_received_count }})
                            </h2>
                            <p
                                v-if="isGuest"
                                class="mb-4 text-sm text-muted-foreground"
                            >
                                <Link
                                    :href="login()"
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
                            <template v-else>
                                <div
                                    v-if="developer.recommendations && developer.recommendations.length > 0"
                                    class="space-y-6"
                                >
                                    <div
                                        v-for="(rec, index) in developer.recommendations"
                                        :key="index"
                                        class="flex gap-4 rounded-xl border border-border bg-card p-5 shadow-sm"
                                    >
                                        <div
                                            class="flex size-12 shrink-0 items-center justify-center rounded-full bg-primary/10 text-lg font-bold text-primary"
                                        >
                                            {{ rec.recommender_name.split(' ').map(n => n[0]).join('').slice(0, 2) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-semibold text-foreground">
                                                {{ rec.recommender_name }}
                                            </p>
                                            <p
                                                v-if="rec.recommender_job_title"
                                                class="text-sm text-muted-foreground"
                                            >
                                                {{ rec.recommender_job_title }}
                                            </p>
                                            <blockquote
                                                v-if="rec.note"
                                                class="mt-3 text-sm leading-relaxed text-muted-foreground"
                                            >
                                                "{{ rec.note }}"
                                            </blockquote>
                                            <p
                                                v-else
                                                class="mt-3 text-sm italic text-muted-foreground"
                                            >
                                                No note provided.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <p
                                    v-else
                                    class="text-sm text-muted-foreground"
                                >
                                    {{ developer.recommendations_received_count }}
                                    {{ developer.recommendations_received_count === 1 ? 'recommendation' : 'recommendations' }}
                                    received.
                                </p>
                            </template>
                        </section>
                    </div>

                    <!-- Sidebar -->
                    <aside>
                        <div
                            class="sticky top-20 rounded-2xl border border-border bg-card p-6 shadow-sm"
                        >
                            <h2
                                class="mb-4 text-xs font-semibold uppercase tracking-widest text-muted-foreground"
                            >
                                Quick Info
                            </h2>
                            <dl class="space-y-4 text-sm">
                                <div>
                                    <dt class="text-muted-foreground">
                                        Role
                                    </dt>
                                    <dd class="mt-0.5 font-semibold">
                                        {{ developer.job_title?.name }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-muted-foreground">
                                        Experience
                                    </dt>
                                    <dd class="mt-0.5 font-semibold">
                                        {{ experienceLabel }}
                                    </dd>
                                </div>
                                <div v-if="developer.location">
                                    <dt class="text-muted-foreground">
                                        Location
                                    </dt>
                                    <dd class="mt-0.5 font-semibold">
                                        {{ developer.location.label }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-muted-foreground">
                                        Availability
                                    </dt>
                                    <dd
                                        class="mt-0.5 font-semibold"
                                        :class="
                                            developer.is_available
                                                ? 'text-availability-available'
                                                : 'text-availability-unavailable'
                                        "
                                    >
                                        {{ developer.is_available ? 'Available' : 'Not Available' }}
                                    </dd>
                                </div>
                                <div v-if="developer.phone">
                                    <dt class="text-muted-foreground">
                                        Phone
                                    </dt>
                                    <dd class="mt-0.5">
                                        <a
                                            :href="`tel:${developer.phone}`"
                                            class="font-semibold text-primary hover:underline"
                                        >
                                            {{ developer.phone }}
                                        </a>
                                    </dd>
                                </div>
                                <div
                                    v-if="developer.expected_salary_from || developer.expected_salary_to"
                                >
                                    <dt class="text-muted-foreground">
                                        Salary
                                    </dt>
                                    <dd class="mt-0.5">
                                        <template v-if="showSalary">
                                            <span class="font-semibold">{{ salaryLabel }}</span>
                                        </template>
                                        <a
                                            v-else
                                            :href="subscribeToSeeSalaryUrl"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="font-semibold text-primary hover:underline"
                                        >
                                            Subscribe to see salary
                                        </a>
                                    </dd>
                                </div>
                            </dl>

                            <div
                                class="mt-6 flex flex-col gap-3 border-t border-border pt-4"
                            >
                                <Button
                                    class="w-full gap-2 rounded-xl"
                                    as-child
                                >
                                    <a :href="`mailto:${developer.email}`">
                                        <Mail class="size-4" />
                                        Contact Now
                                    </a>
                                </Button>
                                <Button
                                    v-if="developer.cv_path_url"
                                    variant="outline"
                                    class="w-full gap-2 rounded-xl"
                                    as-child
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
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </main>

        <Footer />
    </div>
</template>
