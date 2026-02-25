import type { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export type BreadcrumbItem = {
    title: string;
    href?: string;
};

export type NavItem = {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
    /** When false, uses Inertia Link for internal navigation. Default true for external links. */
    external?: boolean;
    /** Spatie permission name (e.g. "View:Users"). Item is hidden if user doesn't have it. */
    permission?: string;
};
