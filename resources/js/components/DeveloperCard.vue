<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    Briefcase,
    ChevronRight,
    Clock,
    Globe,
    Mail,
    MapPin,
    Phone,
    Star,
    ThumbsUp,
    User,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { Developer } from '@/types/developer';

const props = withDefaults(
    defineProps<{
        developer: Developer;
        currentUserDeveloper?: Developer | null;
        recommendedDeveloperIds?: number[];
        contactEmail?: string;
    }>(),
    {
        currentUserDeveloper: null,
        recommendedDeveloperIds: () => [],
        contactEmail: '',
    },
);

const page = usePage();
const auth = computed(() => page.props.auth as { user?: { is_admin?: boolean } } | undefined);
const isAdmin = computed(() => auth.value?.user?.is_admin === true);

const skillsExpanded = ref(false);
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
    if (!props.developer.expected_salary_from && !props.developer.expected_salary_to)
        return false;
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
    const email = props.contactEmail || 'contact@example.com';
    return `mailto:${email}?subject=Subscription%20Inquiry`;
});

function formatNum(n: number): string {
    return new Intl.NumberFormat().format(n);
}
</script>

<template>
    <Card class="relative flex h-full flex-col overflow-hidden border bg-card text-card-foreground shadow-sm">
        <!-- Recommended pill -->
        <div
            v-if="developer.recommended_by_us"
            class="absolute right-3 top-3 z-10 flex items-center gap-1.5 rounded-full border border-primary/30 bg-primary/10 px-2.5 py-1 text-xs font-medium text-primary"
        >
            <Star class="size-3.5 shrink-0" />
            Recommended
        </div>

        <!-- Badges row -->
        <div
            v-if="developer.badges.length > 0"
            class="flex flex-wrap gap-1.5 px-6 pt-6"
        >
            <TooltipProvider v-for="badge in developer.badges" :key="badge.name">
                <Tooltip>
                    <TooltipTrigger as-child>
                        <Link
                            :href="badgesPageUrl"
                            class="inline-flex size-8 items-center justify-center rounded-md border transition-colors hover:opacity-90"
                            :style="
                                badge.color
                                    ? {
                                          background: `${badge.color}20`,
                                          borderColor: `${badge.color}40`,
                                          color: badge.color,
                                      }
                                    : {}
                            "
                        >
                            <span
                                v-if="badge.icon_html"
                                class="size-4 [&>svg]:size-4"
                                v-html="badge.icon_html"
                            />
                        </Link>
                    </TooltipTrigger>
                    <TooltipContent>
                        <p>{{ badge.name }}</p>
                    </TooltipContent>
                </Tooltip>
            </TooltipProvider>
        </div>

        <!-- YouTube embed -->
        <div
            v-if="developer.youtube_video_id"
            class="aspect-video w-full overflow-hidden bg-muted"
        >
            <iframe
                :src="`https://www.youtube.com/embed/${developer.youtube_video_id}?autoplay=0&mute=1&loop=1&playlist=${developer.youtube_video_id}`"
                title="YouTube video"
                class="size-full"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
            />
        </div>

        <CardHeader class="gap-2 pb-2">
            <CardTitle class="text-lg font-semibold leading-tight">
                <Link
                    v-if="profileUrl"
                    :href="profileUrl"
                    class="text-foreground hover:underline"
                >
                    {{ developer.name }}
                </Link>
                <span v-else>{{ developer.name }}</span>
            </CardTitle>
            <Badge variant="secondary" class="w-fit text-xs">
                {{ developer.job_title?.name }}
            </Badge>
        </CardHeader>

        <CardContent class="flex flex-1 flex-col gap-3 pt-0">
            <div class="flex flex-1 flex-col gap-3">
                <!-- Details list -->
                <ul class="flex flex-col gap-2 text-sm text-muted-foreground">
                <li class="flex items-center gap-2">
                    <Briefcase class="size-4 shrink-0 text-muted-foreground" />
                    <span>{{ developer.years_of_experience }} years experience</span>
                </li>
                <li v-if="developer.location" class="flex items-center gap-2">
                    <MapPin class="size-4 shrink-0" />
                    <span>{{ developer.location.label }}</span>
                </li>
                <li v-if="developer.phone" class="flex items-center gap-2">
                    <Phone class="size-4 shrink-0" />
                    <a
                        :href="`tel:${developer.phone}`"
                        class="text-foreground hover:underline"
                    >
                        {{ developer.phone }}
                    </a>
                </li>

                <!-- Salary (admin sees amount; others see subscribe CTA) -->
                <li
                    v-if="
                        developer.expected_salary_from || developer.expected_salary_to
                    "
                    class="flex items-center gap-2"
                >
                    <span class="flex size-4 shrink-0 items-center justify-center text-muted-foreground">
                        <span class="text-base leading-none">$</span>
                    </span>
                    <template v-if="showSalary">
                        <span>{{ salaryLabel }}</span>
                    </template>
                    <template v-else>
                        <a
                            :href="subscribeToSeeSalaryUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-foreground hover:underline"
                        >
                            Subscribe to see salary
                        </a>
                    </template>
                </li>

                <!-- Availability -->
                <li class="flex items-center gap-2">
                    <Clock class="size-4 shrink-0" />
                    <span
                        :class="
                            developer.is_available
                                ? 'text-primary'
                                : 'text-muted-foreground'
                        "
                    >
                        {{ developer.is_available ? 'Available' : 'Not available' }}
                    </span>
                </li>
                <li
                    v-if="
                        developer.availability_type &&
                        developer.availability_type.length > 0
                    "
                    class="flex items-center gap-2"
                >
                    <Clock class="size-4 shrink-0" />
                    <div class="flex flex-wrap gap-1">
                        <Badge
                            v-for="type in developer.availability_type"
                            :key="type.value"
                            variant="outline"
                            class="text-xs"
                        >
                            {{ type.label }}
                        </Badge>
                    </div>
                </li>
                <li
                    v-if="developer.recommendations_received_count > 0"
                    class="flex items-center gap-2"
                >
                    <ThumbsUp class="size-4 shrink-0" />
                    <span class="font-medium">
                        {{ developer.recommendations_received_count }}
                        {{ developer.recommendations_received_count === 1 ? 'Recommendation' : 'Recommendations' }}
                    </span>
                </li>
            </ul>

                <!-- Social / links -->
                <div class="flex flex-wrap gap-2">
                <TooltipProvider v-if="developer.portfolio_url">
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <Button variant="outline" size="icon" as-child class="size-8">
                                <a
                                    :href="developer.portfolio_url!"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <Globe class="size-4" />
                                    <span class="sr-only">Portfolio</span>
                                </a>
                            </Button>
                        </TooltipTrigger>
                        <TooltipContent>Portfolio</TooltipContent>
                    </Tooltip>
                </TooltipProvider>
                <TooltipProvider v-if="developer.github_url">
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <Button variant="outline" size="icon" as-child class="size-8">
                                <a
                                    :href="developer.github_url!"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <svg class="size-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
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
                            <Button variant="outline" size="icon" as-child class="size-8">
                                <a
                                    :href="developer.linkedin_url!"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <svg class="size-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                                    </svg>
                                    <span class="sr-only">LinkedIn</span>
                                </a>
                            </Button>
                        </TooltipTrigger>
                        <TooltipContent>LinkedIn</TooltipContent>
                    </Tooltip>
                </TooltipProvider>
                <TooltipProvider>
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <Button variant="outline" size="icon" as-child class="size-8">
                                <a :href="`mailto:${developer.email}`">
                                    <Mail class="size-4" />
                                    <span class="sr-only">Email</span>
                                </a>
                            </Button>
                        </TooltipTrigger>
                        <TooltipContent>Email</TooltipContent>
                    </Tooltip>
                </TooltipProvider>
                <TooltipProvider v-if="developer.cv_path_url">
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <Button variant="outline" size="icon" as-child class="size-8">
                                <a
                                    :href="developer.cv_path_url!"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="sr-only">CV / Resume</span>
                                </a>
                            </Button>
                        </TooltipTrigger>
                        <TooltipContent>CV / Resume</TooltipContent>
                    </Tooltip>
                </TooltipProvider>
                </div>
            </div>

            <!-- View profile -->
            <Button
                v-if="profileUrl"
                as-child
                variant="ghost"
                class="mt-auto w-full gap-2 border border-border hover:border-primary hover:bg-primary hover:text-primary-foreground"
            >
                <Link :href="profileUrl" class="inline-flex items-center gap-2">
                    <User class="size-4" />
                    View Full Profile
                    <ChevronRight class="size-4" />
                </Link>
            </Button>
        </CardContent>
    </Card>
</template>
