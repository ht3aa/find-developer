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
    | 'viewAnyUser'
    | 'viewAnyRole'
    | 'viewActivityLog'
    | 'viewRecommendationsDashboard';

export type NavItem = {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
    /** When false, uses Inertia Link for internal navigation. Default true for external links. */
    external?: boolean;
    /** Laravel Gate/policy ability key from auth.can. Item is hidden if user doesn't have it. */
    can?: AuthCanKey;
};
