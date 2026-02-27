<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, Moon, Sun } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import {
    NavigationMenu,
    NavigationMenuItem,
    NavigationMenuList,
    navigationMenuTriggerStyle,
} from '@/components/ui/navigation-menu';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { publicMethod as badgesPublic } from '@/routes/badges';
import { dashboard, home, login, logout, register } from '@/routes';
import { useAppearance } from '@/composables/useAppearance';

const page = usePage();
const auth = computed(() => page.props.auth as { user?: { name: string } | null });
const canRegister = computed(() => (page.props.canRegister as boolean) ?? true);
const authCan = computed(() => (page.props.auth as { can?: { viewDeveloperProfile: boolean } })?.can ?? {});
const navItems = [
    { label: 'Home', href: home() },
    { label: 'Badges', href: badgesPublic() },
    { label: 'Charts', href: '/charts' },
];

const { appearance, resolvedAppearance, updateAppearance } = useAppearance();

const themeIcon = computed(() => (resolvedAppearance.value === 'dark' ? Moon : Sun));

const toggleTheme = () => {
    const nextTheme = appearance.value === 'dark' ? 'light' : 'dark';
    updateAppearance(nextTheme);
};
</script>

<template>
    <header
        class="sticky top-0 z-sticky-nav w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60"
    >
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4">
            <!-- Logo -->
            <Link
                :href="home()"
                class="flex items-center gap-2 font-semibold text-foreground"
            >
                <img
                    src="/light-logo.svg"
                    alt=""
                    class="h-8 w-auto shrink-0 sm:h-9"
                />
                <span class="hidden sm:inline-block">Find Developer</span>
            </Link>

            <!-- Desktop navigation -->
            <NavigationMenu class="hidden md:flex">
                <NavigationMenuList class="flex items-center gap-1">
                    <NavigationMenuItem
                        v-for="item in navItems"
                        :key="item.label"
                    >
                        <Link
                            :href="item.href"
                            :class="navigationMenuTriggerStyle()"
                            class="cursor-pointer"
                        >
                            {{ item.label }}
                        </Link>
                    </NavigationMenuItem>
                </NavigationMenuList>
            </NavigationMenu>

            <!-- Desktop auth -->
            <div class="hidden items-center gap-2 md:flex">
                <Button
                    variant="ghost"
                    size="icon"
                    aria-label="Toggle theme"
                    @click="toggleTheme"
                >
                    <component :is="themeIcon" class="size-5" />
                </Button>
                <template v-if="auth.user">
                    <Button v-if="authCan.viewDeveloperProfile" variant="default" as-child>
                        <Link :href="dashboard()">Dashboard</Link>
                    </Button>
                    <Button variant="outline" as-child>
                        <Link :href="logout().url" method="post" as="button">Log out</Link>
                    </Button>
                </template>
                <template v-else>
                    <Button variant="ghost" as-child>
                        <Link :href="login()">Sign In</Link>
                    </Button>
                    <Button v-if="canRegister" variant="default" as-child>
                        <Link :href="register()">Sign Up</Link>
                    </Button>
                </template>
            </div>

            <!-- Mobile menu -->
            <Sheet>
                <SheetTrigger as-child>
                    <Button
                        variant="ghost"
                        size="icon"
                        class="md:hidden"
                        aria-label="Open menu"
                    >
                        <Menu class="size-5" />
                    </Button>
                </SheetTrigger>
                <SheetContent side="left" class="w-[300px] p-0">
                    <SheetHeader class="border-b p-4 text-left">
                        <SheetTitle class="sr-only">Navigation</SheetTitle>
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
                    </SheetHeader>
                    <nav class="flex flex-col gap-1 p-4">
                        <Link
                            v-for="item in navItems"
                            :key="item.label"
                            :href="item.href"
                            class="rounded-md px-3 py-2 text-sm font-medium hover:bg-accent"
                        >
                            {{ item.label }}
                        </Link>
                    </nav>
                    <div class="flex flex-col gap-2 border-t p-4">
                        <Button
                            variant="outline"
                            size="icon"
                            class="w-full"
                            aria-label="Toggle theme"
                            @click="toggleTheme"
                        >
                            <component :is="themeIcon" class="size-5" />
                        </Button>
                        <template v-if="auth.user">
                            <Button v-if="authCan.viewDeveloperProfile" variant="default" as-child class="w-full">
                                <Link :href="dashboard()">Dashboard</Link>
                            </Button>
                            <Button variant="outline" as-child class="w-full">
                                <Link :href="logout().url" method="post" as="button" class="w-full">Log out</Link>
                            </Button>
                        </template>
                        <template v-else>
                            <Button variant="ghost" as-child class="w-full">
                                <Link :href="login()">Sign In</Link>
                            </Button>
                            <Button
                                v-if="canRegister"
                                variant="default"
                                as-child
                                class="w-full"
                            >
                                <Link :href="register()">Sign Up</Link>
                            </Button>
                        </template>
                    </div>
                </SheetContent>
            </Sheet>
        </div>
    </header>
</template>
