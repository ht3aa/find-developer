import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (
                        id.includes('node_modules/vue') ||
                        id.includes('node_modules/@vue') ||
                        id.includes('node_modules/reka-ui')
                    ) {
                        return 'vue-vendor';
                    }
                    if (id.includes('node_modules/@inertiajs')) {
                        return 'inertia-vendor';
                    }
                    if (id.includes('node_modules/chart.js')) {
                        return 'chartjs';
                    }
                    if (id.includes('node_modules/gsap')) {
                        return 'gsap';
                    }
                    if (
                        id.includes('node_modules/@tiptap') ||
                        id.includes('node_modules/prosemirror') ||
                        id.includes('node_modules/@tiptap/pm')
                    ) {
                        return 'tiptap';
                    }
                    if (id.includes('node_modules/@tanstack')) {
                        return 'tanstack';
                    }
                },
            },
        },
    },
});
