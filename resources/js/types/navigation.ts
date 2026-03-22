import type { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export type BreadcrumbItem = {
    title: string;
    href?: string;
};

/** Keys for auth.can shared from Laravel (Gate/policy checks). */
export type AuthCanKey =
    | 'viewAnyDeveloper'
    | 'viewDeveloperProfile'
    | 'viewAnyDeveloperCompany'
    | 'viewAnyDeveloperProject'
    | 'viewAnyDeveloperBlog'
    | 'viewAnyBadge'
    | 'viewAnyHackathon'
    | 'viewAnyUser'
    | 'viewAnyRole'
    | 'viewActivityLog'
    | 'viewRecommendationsDashboard'
    | 'viewDeveloperOffers'
    | 'viewNewsletter'
    | 'viewConversations'
    | 'viewAdminPanel';

export type NavItem = {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
    /** When true, uses a plain anchor for full-page navigation (e.g. Filament /admin) instead of Inertia Link. */
    external?: boolean;
    /** Laravel Gate/policy ability key from auth.can. Item is hidden if user doesn't have it. */
    can?: AuthCanKey;
};
