<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import {
    Briefcase,
    Check,
    ChevronRight,
    Globe,
    Mail,
    MapPin,
    MessageSquare,
    Phone,
    Star,
    ThumbsUp,
    User,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import BadgeIcon from '@/components/BadgeIcon.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import type { Developer } from '@/types/developer';
import { formatSalaryDisplay } from '@/utils/salary';

const props = withDefaults(
    defineProps<{
        developer: Developer;
        currentUserDeveloper?: Developer | null;
        /** When set (logged-in user id), shows Message for other users' profiles. */
        currentUserId?: number | null;
        recommendedDeveloperIds?: number[];
        selectable?: boolean;
        modelValue?: boolean;
        /** When true and the developer has badges, marks the badge row for the welcome tour. */
        tourBadgesAnchor?: boolean;
    }>(),
    {
        currentUserDeveloper: null,
        currentUserId: null,
        recommendedDeveloperIds: () => [],
        selectable: false,
        modelValue: false,
        tourBadgesAnchor: false,
    },
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void;
}>();

function toggleSelect(): void {
    if (props.selectable) {
        emit('update:modelValue', !props.modelValue);
    }
}

const skillsExpanded = ref(false);
const isCardHovered = ref(false);
const visibleSkillsCount = 5;
const hasMoreSkills = computed(
    () => props.developer.skills.length > visibleSkillsCount,
);
const displaySkills = computed(() =>
    skillsExpanded.value
        ? props.developer.skills
        : props.developer.skills.slice(0, visibleSkillsCount),
);
const hiddenSkillsCount = computed(
    () => props.developer.skills.length - visibleSkillsCount,
);

const profileUrl = computed(() => {
    if (props.developer.profile_url) return props.developer.profile_url;
    if (props.developer.slug) return `/developers/${props.developer.slug}`;
    return null;
});

const badgesPageUrl = computed(
    () => props.developer.badges_page_url ?? '/badges',
);

const showSalary = computed(() => {
    const from = props.developer.expected_salary_from;
    const to = props.developer.expected_salary_to;
    return from != null || to != null;
});

const showMessageButton = computed(() => {
    const uid = props.developer.user_id;
    return (
        props.currentUserId != null &&
        uid != null &&
        uid !== props.currentUserId
    );
});

function startConversation(): void {
    const uid = props.developer.user_id;
    if (uid == null) {
        return;
    }
    router.post('/messages', {
        recipient_id: uid,
        body: '',
    });
}

const salaryLabel = computed(() => {
    const d = props.developer;
    const from = d.expected_salary_from;
    const to = d.expected_salary_to;
    const cur = d.currency ?? '';
    const fromFmt = formatSalaryDisplay(from);
    const toFmt = formatSalaryDisplay(to);
    if (from != null && to != null) {
        return `${fromFmt} – ${toFmt} ${cur}`.trim();
    }
    if (from != null) {
        return `From ${fromFmt} ${cur}`.trim();
    }
    if (to != null) {
        return `Up to ${toFmt} ${cur}`.trim();
    }
    return '';
});

function onThumbnailError(e: Event, videoId: string): void {
    const img = e.target as HTMLImageElement;
    if (img) {
        img.src = `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;
    }
}
</script>

<template>
    <Card
        :class="[
            'group relative flex h-full flex-col overflow-hidden rounded-xl border-0 bg-card text-card-foreground shadow-md ring-1 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl',
            modelValue
                ? 'ring-2 shadow-primary/10 ring-primary'
                : 'ring-border/50 hover:ring-primary/20',
        ]"
        @mouseenter="isCardHovered = true"
        @mouseleave="isCardHovered = false"
    >
        <!-- Top accent gradient -->
        <div
            class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-primary via-primary to-secondary opacity-90"
        />

        <!-- Selection checkbox -->
        <button
            v-if="selectable"
            type="button"
            class="absolute top-3.5 left-3.5 z-10 flex size-6 items-center justify-center rounded-md border-2 transition-all duration-200"
            :class="
                modelValue
                    ? 'scale-105 border-primary bg-primary text-primary-foreground shadow-sm shadow-primary/25'
                    : 'border-muted-foreground/30 bg-background/90 backdrop-blur-sm hover:border-primary/60 hover:bg-primary/5'
            "
            :aria-label="`Select ${developer.name}`"
            :aria-pressed="modelValue"
            @click.stop.prevent="toggleSelect"
        >
            <Check v-if="modelValue" class="size-3.5 stroke-[3]" />
        </button>

        <!-- Recommended pill -->
        <div
            v-if="developer.recommended_by_us"
            class="absolute top-4 right-4 z-10 flex items-center gap-1.5 rounded-full border border-primary/40 bg-primary/15 px-3 py-1.5 text-xs font-semibold text-primary shadow-sm backdrop-blur-sm"
        >
            <Star class="size-3.5 shrink-0" />
            Recommended
        </div>

        <!-- YouTube embed -->

        <CardHeader class="space-y-3 px-6 pt-6 pb-3">
            <!-- Badges row -->
            <div
                v-if="developer.badges.length > 0"
                :data-tour="
                    tourBadgesAnchor ? 'developer-card-badges' : undefined
                "
                class="flex flex-wrap gap-2"
            >
                <TooltipProvider
                    v-for="badge in developer.badges"
                    :key="badge.name"
                >
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <Link
                                :href="badgesPageUrl"
                                class="inline-flex size-9 items-center justify-center rounded-lg border transition-all duration-200 hover:scale-110 hover:opacity-100"
                                :class="
                                    badge.color ? '' : 'border-border bg-muted'
                                "
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
                                <BadgeIcon
                                    v-if="badge.icon"
                                    :icon="badge.icon"
                                />
                            </Link>
                        </TooltipTrigger>
                        <TooltipContent>
                            <p>{{ badge.name }}</p>
                        </TooltipContent>
                    </Tooltip>
                </TooltipProvider>
            </div>
            <div
                v-if="developer.youtube_video_id"
                class="relative aspect-video w-full overflow-hidden bg-muted"
            >
                <img
                    v-if="!isCardHovered"
                    :src="`https://img.youtube.com/vi/${developer.youtube_video_id}/maxresdefault.jpg`"
                    :alt="`${developer.name} video thumbnail`"
                    class="size-full object-cover"
                    loading="lazy"
                    @error="
                        onThumbnailError($event, developer.youtube_video_id)
                    "
                />
                <iframe
                    v-else
                    :src="`https://www.youtube.com/embed/${developer.youtube_video_id}?autoplay=1&mute=1&loop=1&playlist=${developer.youtube_video_id}`"
                    title="YouTube video"
                    class="size-full"
                    allow="
                        accelerometer;
                        autoplay;
                        clipboard-write;
                        encrypted-media;
                        gyroscope;
                        picture-in-picture;
                        web-share;
                    "
                    allowfullscreen
                />
            </div>
            <div class="space-y-2">
                <CardTitle
                    class="text-xl leading-tight font-bold tracking-tight"
                >
                    <Link
                        v-if="profileUrl"
                        :href="profileUrl"
                        class="text-foreground transition-colors hover:text-primary"
                    >
                        {{ developer.name }}
                    </Link>
                    <span v-else>{{ developer.name }}</span>
                </CardTitle>
                <Badge
                    variant="outline"
                    class="w-fit rounded-md px-2.5 py-0.5 text-xs font-medium"
                >
                    {{ developer.job_title?.name }}
                </Badge>
            </div>
        </CardHeader>

        <CardContent class="flex flex-1 flex-col gap-4 px-6">
            <!-- Availability pill -->
            <div class="flex flex-wrap items-center gap-2">
                <span
                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                    :class="
                        developer.is_available
                            ? 'bg-availability-available/15 text-availability-available ring-1 ring-availability-available/30'
                            : 'bg-availability-unavailable/15 text-availability-unavailable ring-1 ring-availability-unavailable/30'
                    "
                >
                    <span
                        class="size-1.5 rounded-full"
                        :class="
                            developer.is_available
                                ? 'animate-pulse bg-availability-available'
                                : 'bg-availability-unavailable'
                        "
                    />
                    {{ developer.is_available ? 'Available' : 'Not available' }}
                </span>
                <div
                    v-if="
                        developer.availability_type &&
                        developer.availability_type.length > 0
                    "
                    class="flex flex-wrap gap-1"
                >
                    <Badge
                        v-for="type in developer.availability_type"
                        :key="type.value"
                        variant="outline"
                        class="text-xs font-normal"
                    >
                        {{ type.label }}
                    </Badge>
                </div>
            </div>

            <!-- Details grid -->
            <ul class="flex flex-col gap-2.5 text-sm">
                <li class="flex items-center gap-3 text-muted-foreground">
                    <span
                        class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-muted/80"
                    >
                        <Briefcase class="size-4 text-muted-foreground" />
                    </span>
                    <span class="font-medium text-foreground">
                        {{ developer.years_of_experience }} years experience
                    </span>
                </li>
                <li
                    v-if="developer.location"
                    class="flex items-center gap-3 text-muted-foreground"
                >
                    <span
                        class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-muted/80"
                    >
                        <MapPin class="size-4" />
                    </span>
                    <span>{{ developer.location.label }}</span>
                </li>
                <li
                    v-if="developer.phone"
                    class="flex items-center gap-3 text-muted-foreground"
                >
                    <span
                        class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-muted/80"
                    >
                        <Phone class="size-4" />
                    </span>
                    <a
                        :href="`tel:${developer.phone}`"
                        class="font-medium text-foreground transition-colors hover:text-primary"
                    >
                        {{ developer.phone }}
                    </a>
                </li>

                <!-- Salary -->
                <li
                    v-if="showSalary"
                    class="flex items-center gap-3 text-muted-foreground"
                >
                    <span
                        class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-muted/80"
                    >
                        <span class="text-sm leading-none font-bold">$</span>
                    </span>
                    <span class="font-medium text-foreground">{{
                        salaryLabel
                    }}</span>
                </li>

                <li
                    v-if="developer.recommendations_received_count > 0"
                    class="flex items-center gap-3 text-blue-600 dark:text-blue-400"
                >
                    <span
                        class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-blue-500/15"
                    >
                        <ThumbsUp class="size-4" />
                    </span>
                    <span class="font-medium">
                        {{ developer.recommendations_received_count }}
                        {{
                            developer.recommendations_received_count === 1
                                ? 'Recommendation'
                                : 'Recommendations'
                        }}
                    </span>
                </li>
            </ul>

            <!-- Social links + View profile (grouped at bottom) -->
            <div class="mt-auto flex flex-col gap-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex flex-wrap gap-2">
                        <TooltipProvider v-if="developer.portfolio_url">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button
                                        variant="outline"
                                        size="icon"
                                        as-child
                                        class="size-9 rounded-lg transition-all duration-200 hover:scale-105 hover:border-primary/50 hover:bg-primary/10"
                                    >
                                        <a
                                            :href="developer.portfolio_url!"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            <Globe class="size-4" />
                                            <span class="sr-only"
                                                >Portfolio</span
                                            >
                                        </a>
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>Portfolio</TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider v-if="developer.github_url">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button
                                        variant="outline"
                                        size="icon"
                                        as-child
                                        class="size-9 rounded-lg transition-all duration-200 hover:scale-105 hover:border-primary/50 hover:bg-primary/10"
                                    >
                                        <a
                                            :href="developer.github_url!"
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
                                            <span class="sr-only">GitHub</span>
                                        </a>
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>GitHub</TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider v-if="developer.linkedin_url">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button
                                        variant="outline"
                                        size="icon"
                                        as-child
                                        class="size-9 rounded-lg transition-all duration-200 hover:scale-105 hover:border-primary/50 hover:bg-primary/10"
                                    >
                                        <a
                                            :href="developer.linkedin_url!"
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
                                            <span class="sr-only"
                                                >LinkedIn</span
                                            >
                                        </a>
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>LinkedIn</TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <TooltipProvider v-if="developer.email">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button
                                        variant="outline"
                                        size="icon"
                                        as-child
                                        class="size-9 rounded-lg transition-all duration-200 hover:scale-105 hover:border-primary/50 hover:bg-primary/10"
                                    >
                                        <a :href="`mailto:${developer.email}`">
                                            <Mail class="size-4" />
                                            <span class="sr-only">Email</span>
                                        </a>
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>Email</TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider v-if="showMessageButton">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="icon"
                                        class="size-9 rounded-lg transition-all duration-200 hover:scale-105 hover:border-primary/50 hover:bg-primary/10"
                                        @click.stop.prevent="startConversation"
                                    >
                                        <MessageSquare class="size-4" />
                                        <span class="sr-only">Message</span>
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>Message</TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider v-if="developer.cv_path_url">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button
                                        variant="outline"
                                        size="icon"
                                        as-child
                                        class="size-9 rounded-lg transition-all duration-200 hover:scale-105 hover:border-primary/50 hover:bg-primary/10"
                                    >
                                        <a
                                            :href="developer.cv_path_url!"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            <svg
                                                class="size-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                />
                                            </svg>
                                            <span class="sr-only"
                                                >CV / Resume</span
                                            >
                                        </a>
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>CV / Resume</TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </div>
                </div>

                <!-- View profile CTA -->
                <Button
                    v-if="profileUrl"
                    as-child
                    variant="ghost"
                    class="w-full border border-border hover:border-primary hover:bg-primary hover:text-primary-foreground"
                >
                    <Link
                        :href="profileUrl"
                        class="inline-flex items-center gap-2"
                    >
                        <User class="size-4" />
                        View Full Profile
                        <ChevronRight
                            class="size-4 transition-transform group-hover:translate-x-0.5"
                        />
                    </Link>
                </Button>
            </div>
        </CardContent>
    </Card>
</template>
