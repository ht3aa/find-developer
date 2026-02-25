<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Award, BookOpen, Briefcase, Folder, Home, LayoutGrid, User, Users } from 'lucide-vue-next';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard, home } from '@/routes';
import { index as badgesIndex } from '@/routes/badges';
import { index as developerProfileIndex } from '@/routes/dashboard/developer-profile';
import { index as developersIndex } from '@/routes/developers';
import { index as workExperienceIndex } from '@/routes/work-experience';
import { type NavItem } from '@/types';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Developer Profile',
        href: developerProfileIndex().url,
        icon: User,
    },
    {
        title: 'Work Experience',
        href: workExperienceIndex().url,
        icon: Briefcase,
    },
    {
        title: 'Badges',
        href: badgesIndex(),
        icon: Award,
    },
    {
        title: 'Developers',
        href: developersIndex(),
        icon: Users,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Developers',
        href: home(),
        icon: Home,
        external: false,
    },
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
