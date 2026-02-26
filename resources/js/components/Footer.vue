<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Github, Twitter } from 'lucide-vue-next';
import { Separator } from '@/components/ui/separator';
import { publicMethod as badgesPublic } from '@/routes/badges';
import { home, login, register } from '@/routes';

const footerLinks = [
    { title: 'Home', href: home() },
    { title: 'Badges', href: badgesPublic() },
    { title: 'Sign In', href: login() },
    { title: 'Sign Up', href: register() },
];

const socialLinks = [
    { icon: Github, href: 'https://github.com/ht3aa/find-developer', label: 'GitHub' },
];
</script>

<template>
    <footer
        class="border-t border-border bg-background"
    >
        <div
            class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-6 px-4 py-8 sm:flex-row sm:px-6 lg:px-8"
        >
            <Link
                :href="home()"
                class="flex items-center gap-2 font-semibold text-foreground"
            >
                <img
                    src="/light-logo.svg"
                    alt=""
                    class="h-8 w-auto shrink-0"
                />
                <span>Find Developer</span>
            </Link>

            <nav
                class="flex flex-wrap items-center justify-center gap-6 text-sm text-muted-foreground"
            >
                <Link
                    v-for="{ title, href } in footerLinks"
                    :key="title"
                    :href="href"
                    class="transition-colors hover:text-foreground"
                >
                    {{ title }}
                </Link>
            </nav>
        </div>

        <Separator class="w-full" />

        <div
            class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 py-6 sm:flex-row sm:px-6 lg:px-8"
        >
            <p class="text-sm text-muted-foreground">
                &copy; {{ new Date().getFullYear() }}
                <Link :href="home()" class="font-medium text-foreground hover:underline">
                    Find Developer
                </Link>
                . All rights reserved.
            </p>

            <div class="flex items-center gap-4">
                <Link
                    v-for="item in socialLinks"
                    :key="item.label"
                    :href="item.href"
                    :aria-label="item.label"
                    class="text-muted-foreground transition-colors hover:text-foreground"
                >
                    <component :is="item.icon" class="size-5" aria-hidden="true" />
                </Link>
            </div>
        </div>
    </footer>
</template>
