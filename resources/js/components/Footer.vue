<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Github, Instagram, Linkedin, Send } from 'lucide-vue-next';
import { Separator } from '@/components/ui/separator';
import { home, login, register } from '@/routes';
import { publicMethod as badgesPublic } from '@/routes/badges';
import blogs from '@/routes/blogs';
import { publicMethod as hackathonsPublic } from '@/routes/hackathons';

const supportEmail = 'ht3aa2001@gmail.com';

const supportMailto = `mailto:${supportEmail}`;

const footerLinks = [
    { title: 'Home', href: home(), isMailto: false },
    { title: 'Blog', href: blogs.public.index.url(), isMailto: false },
    { title: 'Badges', href: badgesPublic(), isMailto: false },
    { title: 'Hackathons', href: hackathonsPublic(), isMailto: false },
    { title: 'Support', href: supportMailto, isMailto: true },
    { title: 'Sign In', href: login(), isMailto: false },
    { title: 'Sign Up', href: register(), isMailto: false },
];

const socialLinks = [
    {
        icon: Github,
        href: 'https://github.com/ht3aa/find-developer',
        label: 'GitHub',
    },
    {
        icon: Instagram,
        href: 'https://www.instagram.com/find.developer',
        label: 'Instagram',
    },
    {
        icon: Linkedin,
        href: 'https://www.linkedin.com/company/111716554',
        label: 'LinkedIn',
    },
    {
        icon: Send,
        href: 'https://t.me/finddevelopers',
        label: 'Telegram',
    },
];
</script>

<template>
    <footer class="border-t border-border bg-background">
        <div
            class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-6 px-4 py-8 sm:flex-row sm:px-6 lg:px-8"
        >
            <Link
                :href="home()"
                class="flex items-center gap-2 font-semibold text-foreground"
            >
                <img src="/light-logo.svg" alt="" class="h-8 w-auto shrink-0" />
                <span>Find Developer</span>
            </Link>

            <nav
                class="flex flex-wrap items-center justify-center gap-6 text-sm text-muted-foreground"
            >
                <template
                    v-for="{ title, href, isMailto } in footerLinks"
                    :key="title"
                >
                    <a
                        v-if="isMailto"
                        :href="href"
                        class="transition-colors hover:text-foreground"
                    >
                        {{ title }}
                    </a>
                    <Link
                        v-else
                        :href="href"
                        class="transition-colors hover:text-foreground"
                    >
                        {{ title }}
                    </Link>
                </template>
            </nav>
        </div>

        <Separator class="w-full" />

        <div
            class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 py-6 sm:flex-row sm:px-6 lg:px-8"
        >
            <p class="text-sm text-muted-foreground">
                &copy; {{ new Date().getFullYear() }}
                <Link
                    :href="home()"
                    class="font-medium text-foreground hover:underline"
                >
                    Find Developer
                </Link>
                . All rights reserved. Support:
                <a
                    :href="`mailto:${supportEmail}`"
                    class="font-medium text-foreground hover:underline"
                >
                    {{ supportEmail }}
                </a>
            </p>

            <div class="flex items-center gap-4">
                <a
                    v-for="item in socialLinks"
                    :key="item.label"
                    :href="item.href"
                    :aria-label="item.label"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-muted-foreground transition-colors hover:text-foreground"
                >
                    <component
                        :is="item.icon"
                        class="size-5"
                        aria-hidden="true"
                    />
                </a>
            </div>
        </div>
    </footer>
</template>
