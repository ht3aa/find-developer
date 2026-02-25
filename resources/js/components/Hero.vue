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
        primaryActionLabel: 'Browse developers',
        secondaryActionLabel: 'Sign up',
        canRegister: true,
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
        class="flex flex-col items-center gap-8 px-4 py-16 text-center sm:px-6 sm:py-20 lg:px-8 lg:py-28"
    >
        <div
            class="mx-auto flex max-w-3xl flex-col items-center gap-8 sm:gap-10"
        >
            <div class="flex flex-col gap-4 sm:gap-6">
                <h1
                    class="text-3xl font-bold tracking-tight sm:text-4xl md:text-5xl lg:text-6xl"
                >
                    {{ props.title }}
                </h1>
                <p
                    class="text-base text-muted-foreground sm:text-lg md:text-xl"
                >
                    {{ props.description }}
                </p>
            </div>
            <div
                class="flex flex-wrap items-center justify-center gap-3 sm:gap-4"
            >
                <Button
                    v-if="props.primaryActionHref"
                    as-child
                    size="lg"
                    class="gap-2 text-base"
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
                    v-else
                    size="lg"
                    class="gap-2 text-base"
                    @click="scrollToSearch"
                >
                    {{ props.primaryActionLabel }}
                    <ArrowUpRight class="size-4" aria-hidden="true" />
                </Button>
                <Button as-child variant="outline" size="lg" class="gap-2 text-base">
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
    </section>
</template>
