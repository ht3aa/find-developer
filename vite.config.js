import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/companies.css',
                'resources/js/app.js',
                'resources/css/filament/admin/theme.css',
                'resources/css/base.css',
                'resources/css/layout.css',
                'resources/css/blog.css',
                'resources/css/blogs.css',
                'resources/css/developer-search.css',
                'resources/css/services.css',
                'resources/css/experience-tasks.css',
                'resources/css/developer-recommendations.css',
                'resources/css/developer-projects.css',
                'resources/css/developer-profile.css',
                'resources/css/company-job-search.css',
                'resources/css/charts.css',
                'resources/css/badges.css',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
