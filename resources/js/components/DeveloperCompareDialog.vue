<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Award,
    Briefcase,
    Check,
    ChevronRight,
    Globe,
    MapPin,
    Star,
    ThumbsUp,
    User,
    X,
} from 'lucide-vue-next';
import { computed } from 'vue';
import BadgeIcon from '@/components/BadgeIcon.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import type { Developer } from '@/types/developer';

const props = defineProps<{
    open: boolean;
    developers: [Developer, Developer] | null;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const devs = computed(() => props.developers ?? [null, null]);

const summaryPoints = computed(() => {
    const a = devs.value[0];
    const b = devs.value[1];
    if (!a || !b) return [];

    const points: string[] = [];

    if (a.years_of_experience !== b.years_of_experience) {
        const more = a.years_of_experience > b.years_of_experience ? a : b;
        const less = a.years_of_experience < b.years_of_experience ? a : b;
        points.push(
            `${more.name} has ${more.years_of_experience - less.years_of_experience} more years of experience`,
        );
    }
    if (a.is_available !== b.is_available) {
        const avail = a.is_available ? a : b;
        points.push(`Only ${avail.name} is currently available`);
    }
    if ((a.location?.label ?? '') !== (b.location?.label ?? '')) {
        points.push('Different locations');
    }
    if (a.recommendations_received_count !== b.recommendations_received_count) {
        const more =
            a.recommendations_received_count > b.recommendations_received_count
                ? a
                : b;
        points.push(
            `${more.name} has more recommendations (${more.recommendations_received_count})`,
        );
    }
    if (a.recommended_by_us !== b.recommended_by_us) {
        const rec = a.recommended_by_us ? a : b;
        points.push(`${rec.name} is recommended by us`);
    }
    if (!!a.portfolio_url !== !!b.portfolio_url) {
        const has = a.portfolio_url ? a : b;
        points.push(`${has.name} has a portfolio link`);
    }
    if (a.badges.length !== b.badges.length) {
        const more = a.badges.length > b.badges.length ? a : b;
        const less = a.badges.length < b.badges.length ? a : b;
        points.push(
            `${more.name} has ${more.badges.length - less.badges.length} more badge(s)`,
        );
    }

    const aSkillNames = new Set(a.skills.map((s) => s.name));
    const bSkillNames = new Set(b.skills.map((s) => s.name));
    const onlyA = [...aSkillNames].filter((s) => !bSkillNames.has(s));
    const onlyB = [...bSkillNames].filter((s) => !aSkillNames.has(s));
    if (onlyA.length > 0 || onlyB.length > 0) {
        if (onlyA.length > 0 && onlyB.length > 0) {
            points.push(
                `Different skill sets — ${a.name} has ${onlyA.length} unique, ${b.name} has ${onlyB.length} unique`,
            );
        } else if (onlyA.length > 0) {
            points.push(`${a.name} has ${onlyA.length} additional skill(s)`);
        } else {
            points.push(`${b.name} has ${onlyB.length} additional skill(s)`);
        }
    }

    return points;
});

const comparisonRows = computed(() => {
    const a = devs.value[0];
    const b = devs.value[1];
    if (!a || !b) return [];

    return [
        {
            label: 'Years of experience',
            icon: Briefcase,
            a: `${a.years_of_experience} years`,
            b: `${b.years_of_experience} years`,
            highlight: a.years_of_experience !== b.years_of_experience,
        },
        {
            label: 'Availability',
            icon: Check,
            a: a.is_available ? 'Available' : 'Not available',
            b: b.is_available ? 'Available' : 'Not available',
            highlight: a.is_available !== b.is_available,
        },
        {
            label: 'Location',
            icon: MapPin,
            a: a.location?.label ?? '—',
            b: b.location?.label ?? '—',
            highlight:
                (a.location?.label ?? '') !== (b.location?.label ?? ''),
        },
        {
            label: 'Recommendations',
            icon: ThumbsUp,
            a: String(a.recommendations_received_count),
            b: String(b.recommendations_received_count),
            highlight:
                a.recommendations_received_count !==
                b.recommendations_received_count,
        },
        {
            label: 'Recommended by us',
            icon: Star,
            a: a.recommended_by_us ? 'Yes' : 'No',
            b: b.recommended_by_us ? 'Yes' : 'No',
            highlight: a.recommended_by_us !== b.recommended_by_us,
        },
        {
            label: 'Portfolio',
            icon: Globe,
            a: a.portfolio_url ? 'Yes' : 'No',
            b: b.portfolio_url ? 'Yes' : 'No',
            highlight: !!a.portfolio_url !== !!b.portfolio_url,
        },
        {
            label: 'Badges',
            icon: Award,
            a:
                a.badges.length > 0
                    ? `${a.badges.length} badge${a.badges.length === 1 ? '' : 's'}`
                    : '—',
            b:
                b.badges.length > 0
                    ? `${b.badges.length} badge${b.badges.length === 1 ? '' : 's'}`
                    : '—',
            highlight: a.badges.length !== b.badges.length,
        },
    ];
});

function profileUrl(d: Developer): string | null {
    if (d.profile_url) return d.profile_url;
    if (d.slug) return `/developers/${d.slug}`;
    return null;
}

function close(): void {
    emit('update:open', false);
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent
            class="max-h-[90vh] overflow-y-auto p-0"
            :style="{ width: 'min(95vw, 1600px)', maxWidth: '95vw' }"
            :show-close-button="false"
        >
            <div class="sticky top-0 z-10 flex flex-col gap-4 border-b border-border bg-background/95 px-6 py-4 backdrop-blur supports-[backdrop-filter]:bg-background/80">
                <div class="flex items-center justify-between">
                    <DialogHeader>
                        <DialogTitle class="text-lg font-semibold">
                            Compare developers
                        </DialogTitle>
                        <DialogDescription class="sr-only">
                            Side-by-side comparison of two developer profiles
                        </DialogDescription>
                    </DialogHeader>
                    <Button
                        variant="ghost"
                        size="icon"
                        class="shrink-0 rounded-full"
                        aria-label="Close comparison"
                        @click="close"
                    >
                        <X class="size-5" />
                    </Button>
                </div>
                <div
                    v-if="developers && developers.length === 2 && summaryPoints.length > 0"
                    class="rounded-lg border border-primary/20 bg-primary/5 px-4 py-3"
                >
                    <p class="mb-2 text-sm font-semibold text-foreground">
                        Key differences
                    </p>
                    <ul class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-muted-foreground">
                        <li
                            v-for="(point, i) in summaryPoints"
                            :key="i"
                            class="flex items-center gap-2"
                        >
                            <span
                                class="size-1.5 shrink-0 rounded-full bg-primary"
                            />
                            {{ point }}
                        </li>
                    </ul>
                </div>
            </div>

            <div
                v-if="developers && developers.length === 2"
                class="grid gap-0 md:grid-cols-2"
            >
                <!-- Column headers -->
                <div
                    class="flex flex-col gap-4 border-b border-border p-6 md:border-b-0 md:border-r md:pb-6"
                >
                    <div class="flex flex-col items-start gap-3">
                        <div class="flex w-full items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <h3
                                    class="truncate text-xl font-bold tracking-tight text-foreground"
                                >
                                    {{ developers[0].name }}
                                </h3>
                                <Badge
                                    variant="outline"
                                    class="mt-1.5 w-fit text-xs"
                                >
                                    {{ developers[0].job_title?.name }}
                                </Badge>
                            </div>
                            <Link
                                v-if="profileUrl(developers[0])"
                                :href="profileUrl(developers[0]) ?? '#'"
                                class="shrink-0"
                            >
                                <Button
                                    variant="outline"
                                    size="sm"
                                    class="gap-1.5"
                                >
                                    <User class="size-3.5" />
                                    Profile
                                    <ChevronRight class="size-3.5" />
                                </Button>
                            </Link>
                        </div>
                        <div
                            v-if="developers[0].badges.length > 0"
                            class="flex flex-wrap gap-1.5"
                        >
                            <TooltipProvider
                                v-for="badge in developers[0].badges"
                                :key="badge.name"
                            >
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <Link
                                            :href="
                                                developers[0].badges_page_url ??
                                                '/badges'
                                            "
                                            class="inline-flex size-8 items-center justify-center rounded-lg border transition-all duration-200 hover:scale-110 hover:opacity-100"
                                            :class="
                                                badge.color
                                                    ? ''
                                                    : 'border-border bg-muted'
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
                                                icon-class="size-4"
                                            />
                                        </Link>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{{ badge.name }}</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                        </div>
                    </div>
                </div>
                <div
                    class="flex flex-col gap-4 border-b border-border p-6 md:border-b-0 md:pb-6"
                >
                    <div class="flex flex-col items-start gap-3">
                        <div class="flex w-full items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <h3
                                    class="truncate text-xl font-bold tracking-tight text-foreground"
                                >
                                    {{ developers[1].name }}
                                </h3>
                                <Badge
                                    variant="outline"
                                    class="mt-1.5 w-fit text-xs"
                                >
                                    {{ developers[1].job_title?.name }}
                                </Badge>
                            </div>
                            <Link
                                v-if="profileUrl(developers[1])"
                                :href="profileUrl(developers[1]) ?? '#'"
                                class="shrink-0"
                            >
                                <Button
                                    variant="outline"
                                    size="sm"
                                    class="gap-1.5"
                                >
                                    <User class="size-3.5" />
                                    Profile
                                    <ChevronRight class="size-3.5" />
                                </Button>
                            </Link>
                        </div>
                        <div
                            v-if="developers[1].badges.length > 0"
                            class="flex flex-wrap gap-1.5"
                        >
                            <TooltipProvider
                                v-for="badge in developers[1].badges"
                                :key="badge.name"
                            >
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <Link
                                            :href="
                                                developers[1].badges_page_url ??
                                                '/badges'
                                            "
                                            class="inline-flex size-8 items-center justify-center rounded-lg border transition-all duration-200 hover:scale-110 hover:opacity-100"
                                            :class="
                                                badge.color
                                                    ? ''
                                                    : 'border-border bg-muted'
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
                                                icon-class="size-4"
                                            />
                                        </Link>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{{ badge.name }}</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                        </div>
                    </div>
                </div>

                <!-- Comparison rows -->
                <div
                    class="col-span-full overflow-hidden rounded-lg border border-border md:mx-4 md:mb-4"
                >
                    <p
                        class="border-b border-border bg-muted/30 px-5 py-2.5 text-xs font-semibold uppercase tracking-wider text-muted-foreground"
                    >
                        Overview
                    </p>
                    <div class="grid md:grid-cols-2">
                        <template
                            v-for="(row, idx) in comparisonRows"
                            :key="idx"
                        >
                            <div
                                class="flex items-center gap-4 border-b border-border px-5 py-4 transition-colors last:border-b-0 md:border-b-0 md:border-r md:py-4"
                                :class="[
                                    idx % 2 === 0 ? 'md:bg-muted/20' : '',
                                    row.highlight
                                        ? 'border-l-4 border-l-primary bg-primary/5 md:odd:border-l-4 md:odd:border-l-primary md:odd:bg-primary/5'
                                        : 'border-l-4 border-l-transparent',
                                ]"
                            >
                                <span
                                    class="flex size-10 shrink-0 items-center justify-center rounded-xl transition-colors"
                                    :class="
                                        row.highlight
                                            ? 'bg-primary/15 text-primary'
                                            : 'bg-muted/60 text-muted-foreground'
                                    "
                                >
                                    <component
                                        :is="row.icon"
                                        class="size-5"
                                    />
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                        {{ row.label }}
                                    </p>
                                    <p class="mt-1 text-base font-semibold text-foreground">
                                        {{ row.a }}
                                    </p>
                                </div>
                            </div>
                            <div
                                class="flex items-center gap-4 border-b border-border px-5 py-4 transition-colors last:border-b-0 md:border-b-0 md:py-4"
                                :class="[
                                    idx % 2 === 1 ? 'md:bg-muted/20' : '',
                                    row.highlight
                                        ? 'border-l-4 border-l-primary bg-primary/5 md:even:border-l-4 md:even:border-l-primary md:even:bg-primary/5'
                                        : 'border-l-4 border-l-transparent',
                                ]"
                            >
                                <span
                                    class="flex size-10 shrink-0 items-center justify-center rounded-xl transition-colors"
                                    :class="
                                        row.highlight
                                            ? 'bg-primary/15 text-primary'
                                            : 'bg-muted/60 text-muted-foreground'
                                    "
                                >
                                    <component
                                        :is="row.icon"
                                        class="size-5"
                                    />
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                        {{ row.label }}
                                    </p>
                                    <p class="mt-1 text-base font-semibold text-foreground">
                                        {{ row.b }}
                                    </p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Skills section -->
                <div
                    class="col-span-full grid gap-0 border-t border-border md:grid-cols-2"
                >
                    <div
                        class="border-b border-border px-6 py-4 md:border-b-0 md:border-r"
                    >
                        <p class="mb-2 text-xs font-medium text-muted-foreground">
                            Skills
                        </p>
                        <div class="flex flex-wrap gap-1.5">
                            <Badge
                                v-for="skill in developers[0].skills"
                                :key="skill.name"
                                variant="secondary"
                                class="text-xs font-normal"
                            >
                                {{ skill.name }}
                            </Badge>
                            <span
                                v-if="developers[0].skills.length === 0"
                                class="text-sm text-muted-foreground"
                            >
                                —
                            </span>
                        </div>
                    </div>
                    <div class="border-b border-border px-6 py-4">
                        <p class="mb-2 text-xs font-medium text-muted-foreground">
                            Skills
                        </p>
                        <div class="flex flex-wrap gap-1.5">
                            <Badge
                                v-for="skill in developers[1].skills"
                                :key="skill.name"
                                variant="secondary"
                                class="text-xs font-normal"
                            >
                                {{ skill.name }}
                            </Badge>
                            <span
                                v-if="developers[1].skills.length === 0"
                                class="text-sm text-muted-foreground"
                            >
                                —
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Availability types -->
                <div
                    class="col-span-full grid gap-0 border-t border-border md:grid-cols-2"
                >
                    <div
                        class="border-b border-border px-6 py-4 md:border-b-0 md:border-r"
                    >
                        <p class="mb-2 text-xs font-medium text-muted-foreground">
                            Availability type
                        </p>
                        <div class="flex flex-wrap gap-1.5">
                            <Badge
                                v-for="type in developers[0].availability_type"
                                :key="type.value"
                                variant="outline"
                                class="text-xs font-normal"
                            >
                                {{ type.label }}
                            </Badge>
                            <span
                                v-if="
                                    developers[0].availability_type.length === 0
                                "
                                class="text-sm text-muted-foreground"
                            >
                                —
                            </span>
                        </div>
                    </div>
                    <div class="border-b border-border px-6 py-4">
                        <p class="mb-2 text-xs font-medium text-muted-foreground">
                            Availability type
                        </p>
                        <div class="flex flex-wrap gap-1.5">
                            <Badge
                                v-for="type in developers[1].availability_type"
                                :key="type.value"
                                variant="outline"
                                class="text-xs font-normal"
                            >
                                {{ type.label }}
                            </Badge>
                            <span
                                v-if="
                                    developers[1].availability_type.length === 0
                                "
                                class="text-sm text-muted-foreground"
                            >
                                —
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
