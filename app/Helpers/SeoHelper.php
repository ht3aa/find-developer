<?php

namespace App\Helpers;

class SeoHelper
{
    /**
     * Get default SEO data for the application
     */
    public static function getDefaultSeo(): array
    {
        return [
            'title' => 'FindDeveloper - Find Your Perfect Developer | Developer Search Platform',
            'description' => 'Find and connect with skilled developers in Iraq. Search by skills, experience, location, and job title. Browse developer portfolios, projects, and contact information.',
            'keywords' => 'find developer, developer search, hire developer, developers in iraq, web developer, mobile developer, software developer, developer portfolio, freelance developer',
            'image' => asset('images/og-image.jpg'),
            'url' => url('/'),
            'type' => 'website',
            'site_name' => 'FindDeveloper',
        ];
    }

    /**
     * Get SEO data for a specific page
     */
    public static function getPageSeo(string $page, array $custom = []): array
    {
        $default = self::getDefaultSeo();

        $pages = [
            'experience-tasks' => [
                'title' => 'Get Experience - FindDeveloper | Build Experience with Small Tasks',
                'description' => 'Browse small tasks to build your experience, earn XP, and grow as a developer.',
                'keywords' => 'get experience, developer tasks, build experience, earn XP, practice projects',
            ],
            'home' => [
                'title' => 'FindDeveloper - Find Your Perfect Developer | Developer Search Platform',
                'description' => 'Search and discover talented developers in Iraq. Filter by skills, experience, location, and job title. Connect with developers for your projects.',
                'keywords' => 'find developer, developer search, hire developer, developers in iraq, search developers, developer directory',
            ],
            'plans' => [
                'title' => 'Pricing Plans - FindDeveloper | Developer & Student Plans',
                'description' => 'Choose the perfect plan for developers or students. Free developer profiles, Pro plans, Premium plans, and student portfolio packages. Affordable pricing in IQD.',
                'keywords' => 'developer plans, pricing plans, student portfolio, developer subscription, pro plan, premium plan, portfolio packages',
            ],
            'about' => [
                'title' => 'About Us - FindDeveloper | Connecting Developers with Opportunities',
                'description' => 'Learn about FindDeveloper - a platform connecting skilled developers with clients and opportunities. Discover our mission and services.',
                'keywords' => 'about finddeveloper, developer platform, connect developers, developer marketplace',
            ],
            'register' => [
                'title' => 'Register as Developer - FindDeveloper | Create Your Developer Profile',
                'description' => 'Register as a developer on FindDeveloper. Create your profile, showcase your skills, projects, and connect with clients. Free registration available.',
                'keywords' => 'register developer, developer registration, create developer profile, join developers, developer signup',
            ],
        ];

        $pageData = $pages[$page] ?? $default;

        return array_merge($default, $pageData, $custom);
    }

    /**
     * Generate structured data (JSON-LD) for Organization
     */
    public static function getOrganizationStructuredData(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'FindDeveloper',
            'url' => url('/'),
            'description' => 'A platform connecting skilled developers with clients and opportunities in Iraq',
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'Customer Service',
                'availableLanguage' => ['English', 'Arabic'],
            ],
        ];
    }

    /**
     * Generate structured data (JSON-LD) for Website
     */
    public static function getWebsiteStructuredData(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'FindDeveloper',
            'url' => url('/'),
            'description' => 'Find and connect with skilled developers in Iraq',
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => url('/') . '?search={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * Generate structured data (JSON-LD) for BreadcrumbList
     */
    public static function getBreadcrumbStructuredData(array $items): array
    {
        $breadcrumbItems = [];
        $position = 1;

        foreach ($items as $item) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $item['name'],
                'item' => $item['url'] ?? url('/'),
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbItems,
        ];
    }
}
