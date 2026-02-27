<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { User } from 'lucide-vue-next';
import Footer from '@/components/Footer.vue';
import Navbar from '@/components/Navbar.vue';
import SeoHead from '@/components/SeoHead.vue';
import type { PublicBlogDetail } from '@/types/developer-blog';

const props = defineProps<{
    blog: PublicBlogDetail;
}>();

const page = usePage();
const appUrl = computed(() => (page.props.appUrl as string) ?? '');

function formatDate(iso: string | null): string {
    if (!iso) return '';
    const d = new Date(iso);
    return d.toLocaleDateString(undefined, { dateStyle: 'long' });
}

const blogCanonical = computed(() => `/blogs/${props.blog.slug}`);

const articleJsonLd = computed(() => {
    const b = props.blog;
    const base: Record<string, unknown> = {
        '@context': 'https://schema.org',
        '@type': 'Article',
        headline: b.title,
        description: b.excerpt ?? undefined,
        datePublished: b.published_at ?? undefined,
        ...(b.featured_image_url ? { image: b.featured_image_url } : {}),
    };
    const url = appUrl.value ? `${appUrl.value.replace(/\/$/, '')}${blogCanonical.value}` : undefined;
    if (url) base.url = url;
    if (b.developer) {
        base.author = {
            '@type': 'Person',
            name: b.developer.name,
            url: appUrl.value ? `${appUrl.value.replace(/\/$/, '')}/developers/${b.developer.slug}` : undefined,
        };
    }
    return base;
});
</script>

<template>
    <SeoHead
        :title="blog.title"
        :description="blog.excerpt"
        :image="blog.featured_image_url"
        :canonical="blogCanonical"
        og-type="article"
        :json-ld="articleJsonLd"
    />
    <Head>
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <article class="mx-auto w-full max-w-3xl flex-1 px-4 py-12 sm:px-6 lg:px-8">
            <header class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                    {{ blog.title }}
                </h1>
                <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-muted-foreground">
                    <time :datetime="blog.published_at ?? undefined">
                        {{ formatDate(blog.published_at) }}
                    </time>
                    <Link
                        v-if="blog.developer"
                        :href="`/developers/${blog.developer.slug}`"
                        class="flex items-center gap-1.5 font-medium text-foreground hover:underline"
                    >
                        <User class="size-4" />
                        {{ blog.developer.name }}
                    </Link>
                </div>
            </header>

            <div
                v-if="blog.featured_image_url"
                class="mb-8 aspect-video w-full overflow-hidden rounded-xl bg-muted"
            >
                <img
                    :src="blog.featured_image_url"
                    :alt="blog.title"
                    class="size-full object-cover"
                />
            </div>

            <div
                v-if="blog.excerpt"
                class="mb-6 text-lg text-muted-foreground"
            >
                {{ blog.excerpt }}
            </div>

            <div
                class="prose prose-neutral dark:prose-invert max-w-none"
                v-html="blog.content"
            />

            <footer class="mt-12 border-t pt-8">
                <Link
                    href="/blogs"
                    class="text-sm font-medium text-primary hover:underline"
                >
                    ← Back to blog
                </Link>
            </footer>
        </article>

        <Footer />
    </div>
</template>
