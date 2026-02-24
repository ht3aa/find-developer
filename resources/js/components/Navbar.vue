<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Menu } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
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
import { dashboard, home, login, register } from '@/routes';

const page = usePage();
const auth = computed(() => page.props.auth as { user?: { name: string } | null });
const canRegister = computed(() => (page.props.canRegister as boolean) ?? true);

const navItems = [
    { label: 'Home', href: home() },
    { label: 'Blog', href: '#' },
    { label: 'About', href: '#about' },
    { label: 'Contact Us', href: '#contact' },
];
</script>

<template>
    <header
        class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60"
    >
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4">
            <!-- Logo -->
            <Link
                :href="home()"
                class="flex items-center gap-2 font-semibold text-foreground"
            >
                <AppLogoIcon
                    class="size-6 fill-current text-foreground"
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
                <template v-if="auth.user">
                    <Button variant="default" as-child>
                        <Link :href="dashboard()">Dashboard</Link>
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
                            <AppLogoIcon class="size-6 fill-current" />
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
                        <template v-if="auth.user">
                            <Button variant="default" as-child class="w-full">
                                <Link :href="dashboard()">Dashboard</Link>
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
