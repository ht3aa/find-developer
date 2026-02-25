<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowUpRight, CirclePlay } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { login } from '@/routes';
import { register } from '@/routes';

const props = withDefaults(
    defineProps<{
        badge?: string;
        title?: string;
        description?: string;
        primaryActionLabel?: string;
        primaryActionHref?: string;
        secondaryActionLabel?: string;
        canRegister?: boolean;
    }>(),
    {
        badge: 'Find developers',
        title: 'Find the right developer for your project',
        description:
            'Browse vetted developers, filter by skills and experience, and connect with the best match for your team.',
    },
);

function scrollToSearch(): void {
    document
        .querySelector('[data-hero-search]')
        ?.scrollIntoView({ behavior: 'smooth' });
}
</script>

<template>
    <section
        class="relative overflow-hidden border-b border-border bg-gradient-to-b from-background via-muted/30 to-background"
    >
        <!-- Decorative background elements -->
        <div
            class="pointer-events-none absolute inset-0 overflow-hidden"
            aria-hidden="true"
        >
            <div
                class="absolute -left-40 -top-40 size-80 rounded-full bg-primary/10 blur-3xl"
            />
            <div
                class="absolute -right-40 top-1/2 size-96 -translate-y-1/2 rounded-full bg-secondary/10 blur-3xl"
            />
            <div
                class="absolute bottom-0 left-1/2 -translate-x-1/2 size-[500px] rounded-full bg-primary/5 blur-3xl"
            />
        </div>

        <div class="relative mx-auto max-w-5xl px-4 py-20 sm:px-6 sm:py-24 lg:px-8 lg:py-32">
            <div class="flex flex-col items-center text-center">
                <!-- Badge -->
                <Badge
                    v-if="props.badge != null"
                    variant="secondary"
                    class="mb-6 rounded-full border border-primary/20 bg-primary/5 px-4 py-1.5 text-sm font-medium text-primary shadow-sm"
                >
                    {{ props.badge }}
                </Badge>

                <!-- Title -->
                <h1
                    v-if="props.title != null"
                    class="text-3xl font-bold tracking-tight sm:text-4xl md:text-5xl lg:text-6xl"
                >
                    {{ props.title }}
                </h1>
                <p
                    v-if="props.description != null"
                    class="mt-4 max-w-2xl text-base text-muted-foreground sm:text-lg md:text-xl"
                >
                    {{ props.description }}
                </p>

                <!-- CTA buttons -->
                <div
                    v-if="props.primaryActionLabel != null || props.secondaryActionLabel != null"
                    class="mt-10 flex flex-wrap items-center justify-center gap-3 sm:gap-4"
                >
                    <Button
                        v-if="props.primaryActionLabel != null && props.primaryActionHref"
                        as-child
                        size="lg"
                        class="gap-2 rounded-xl text-base shadow-lg transition-all duration-200 hover:shadow-xl"
                    >
                        <Link
                            :href="props.primaryActionHref"
                            class="inline-flex items-center gap-2"
                        >
                            {{ props.primaryActionLabel }}
                            <ArrowUpRight class="size-4" aria-hidden="true" />
                        </Link>
                    </Button>
                    <Button
                        v-else-if="props.primaryActionLabel != null"
                        size="lg"
                        class="gap-2 rounded-xl text-base shadow-lg transition-all duration-200 hover:shadow-xl"
                        @click="scrollToSearch"
                    >
                        {{ props.primaryActionLabel }}
                        <ArrowUpRight class="size-4" aria-hidden="true" />
                    </Button>
                    <Button
                        v-if="props.secondaryActionLabel != null"
                        as-child
                        variant="outline"
                        size="lg"
                        class="gap-2 rounded-xl border-2 text-base transition-all duration-200 hover:border-primary/50 hover:bg-primary/5"
                    >
                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="inline-flex items-center gap-2"
                        >
                            {{ props.secondaryActionLabel }}
                            <CirclePlay class="size-4" aria-hidden="true" />
                        </Link>
                        <Link
                            v-else
                            :href="login()"
                            class="inline-flex items-center gap-2"
                        >
                            Log in
                            <ArrowUpRight class="size-4" aria-hidden="true" />
                        </Link>
                    </Button>
                </div>
            </div>
        </div>

        <!-- Bottom accent gradient -->
        <div
            class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-transparent via-primary/60 to-transparent opacity-80"
        />
    </section>
</template>
