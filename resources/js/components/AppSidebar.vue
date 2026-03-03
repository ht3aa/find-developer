<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Award, BookOpen, Briefcase, ClipboardList, FileText, Folder, FolderKanban, Home, LayoutGrid, Mail, Shield, ThumbsUp, Trophy, User, UserCog, Users } from 'lucide-vue-next';
import { computed } from 'vue';
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
import { index as activityLogIndex } from '@/routes/dashboard/activity-log';
import { index as developerProfileIndex } from '@/routes/dashboard/developer-profile';
import { index as newsletterIndex } from '@/routes/dashboard/newsletter';
import { index as developerBlogsIndex } from '@/routes/developer-blogs';
import { index as developerProjectsIndex } from '@/routes/developer-projects';
import { index as developerRecommendationsIndex } from '@/routes/developer-recommendations';
import { index as developersIndex } from '@/routes/developers';
import { index as hackathonsIndex } from '@/routes/hackathons';
import rolesRoutes from '@/routes/roles';
import usersRoutes from '@/routes/users';
import { index as workExperienceIndex } from '@/routes/work-experience';
import { type AuthCanKey, type NavItem } from '@/types';
import AppLogo from './AppLogo.vue';

const page = usePage();
const authCan = computed(() => (page.props.auth as { can?: Record<AuthCanKey, boolean> })?.can ?? {});

function canSeeNavItem(item: NavItem): boolean {
    if (!item.can) return true;
    return authCan.value[item.can] === true;
}

const allMainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
        can: 'viewDeveloperProfile',
    },
    {
        title: 'Developer Profile',
        href: developerProfileIndex().url,
        icon: User,
        can: 'viewDeveloperProfile',
    },
    {
        title: 'Work Experience',
        href: workExperienceIndex().url,
        icon: Briefcase,
        can: 'viewAnyDeveloperCompany',
    },
    {
        title: 'Projects',
        href: developerProjectsIndex().url,
        icon: FolderKanban,
        can: 'viewAnyDeveloperProject',
    },
    {
        title: 'Blogs',
        href: developerBlogsIndex().url,
        icon: FileText,
        can: 'viewAnyDeveloperBlog',
    },
    {
        title: 'Badges',
        href: badgesIndex(),
        icon: Award,
        can: 'viewAnyBadge',
    },
    {
        title: 'Hackathons',
        href: hackathonsIndex(),
        icon: Trophy,
        can: 'viewAnyHackathon',
    },
    {
        title: 'Developers',
        href: developersIndex(),
        icon: Users,
        can: 'viewAnyDeveloper',
    },
    {
        title: 'Users',
        href: usersRoutes.index.url(),
        icon: UserCog,
        can: 'viewAnyUser',
    },
    {
        title: 'Roles',
        href: rolesRoutes.index.url(),
        icon: Shield,
        can: 'viewAnyRole',
    },
    {
        title: 'Newsletter',
        href: newsletterIndex().url,
        icon: Mail,
        can: 'viewNewsletter',
    },
    {
        title: 'Activity Log',
        href: activityLogIndex().url,
        icon: ClipboardList,
        can: 'viewActivityLog',
    },
    {
        title: 'Recommendations',
        href: developerRecommendationsIndex().url,
        icon: ThumbsUp,
        can: 'viewRecommendationsDashboard',
    },
];

const mainNavItems = computed(() => allMainNavItems.filter(canSeeNavItem));

const supportEmail = 'ht3aa2001@gmail.com';

const footerNavItems: NavItem[] = [
    {
        title: 'Home',
        href: home(),
        icon: Home,
        external: false,
    },
    {
        title: 'Support',
        href: `mailto:${supportEmail}`,
        icon: Mail,
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
