<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Award, BookOpen, Briefcase, Folder, Home, LayoutGrid, Shield, User, UserCog, Users } from 'lucide-vue-next';
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
import { index as rolesIndex } from '@/routes/roles';
import { index as usersIndex } from '@/routes/users';
import { index as workExperienceIndex } from '@/routes/work-experience';
import { type NavItem } from '@/types';
import AppLogo from './AppLogo.vue';

const page = usePage();
const userPermissions = computed(() => (page.props.auth as { permissions?: string[] })?.permissions ?? []);

function canSeeNavItem(item: NavItem): boolean {
    if (!item.permission) return true;
    return userPermissions.value.includes(item.permission);
}

const allMainNavItems: NavItem[] = [
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
    {
        title: 'Users',
        href: usersIndex(),
        icon: UserCog,
        permission: 'View:Users',
    },
    {
        title: 'Roles',
        href: rolesIndex(),
        icon: Shield,
        permission: 'View:Roles',
    },
];

const mainNavItems = computed(() => allMainNavItems.filter(canSeeNavItem));

const footerNavItems: NavItem[] = [
    {
        title: 'Home',
        href: home(),
        icon: Home,
        external: false,
    },
    {
        title: 'Github Repo',
        href: 'https://github.com/ht3aa/find-developer',
        icon: Folder,
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
