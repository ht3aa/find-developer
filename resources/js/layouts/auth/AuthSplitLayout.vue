<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative grid min-h-svh flex-col items-center justify-center lg:max-w-none lg:grid-cols-2 lg:px-0"
    >
        <!-- Left: logo / branding (Login 04 style) -->
        <div
            class="relative hidden flex-col bg-muted p-10 text-white lg:sticky lg:top-0 lg:flex lg:h-dvh lg:self-start dark:border-r dark:bg-zinc-900/95"
        >
            <Link
                :href="home()"
                class="absolute left-10 top-10 z-10 font-medium text-foreground/80 transition hover:text-foreground"
            >
                Back to home
            </Link>
            <div
                class="flex min-h-0 flex-1 flex-col p-8 pt-16"
            >
                <iframe
                    src="https://www.youtube.com/embed/qjpk3l45hfo?autoplay=1&mute=0&loop=1&playlist=qjpk3l45hfo"
                    title="Video"
                    class="min-h-0 flex-1 w-full rounded-lg"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                />
            </div>
        </div>

        <!-- Right: content -->
        <div class="flex flex-col p-6 sm:p-10 lg:p-8">
            <div
                class="mx-auto flex w-full flex-col justify-center gap-6 sm:w-[380px]"
            >
                <div
                    v-if="title || description"
                    class="flex flex-col gap-2 text-center lg:text-left"
                >
                    <h1
                        v-if="title"
                        class="text-2xl font-semibold tracking-tight"
                    >
                        {{ title }}
                    </h1>
                    <p
                        v-if="description"
                        class="text-sm text-muted-foreground"
                    >
                        {{ description }}
                    </p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
