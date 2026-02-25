<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { FileText, User } from 'lucide-vue-next';
import Footer from '@/components/Footer.vue';
import Hero from '@/components/Hero.vue';
import Navbar from '@/components/Navbar.vue';
import Pagination from '@/components/Pagination.vue';
import { Card, CardContent } from '@/components/ui/card';
import type { PublicBlogEntry } from '@/types/developer-blog';

type PaginationLink = { url: string | null; label: string; active: boolean };

type Props = {
    blogs: {
        data: PublicBlogEntry[];
        links: PaginationLink[];
        current_page: number;
        last_page: number;
        total: number;
        from: number | null;
        to: number | null;
    };
};

const props = defineProps<Props>();

function formatDate(iso: string | null): string {
    if (!iso) return '';
    const d = new Date(iso);
    return d.toLocaleDateString(undefined, { dateStyle: 'medium' });
}
</script>

<template>
    <Head title="Blog">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <Hero
            badge="Developer blog"
            title="Blog"
            description="Articles and posts from our developers. Read about their experience, tips, and insights."
        />

        <section class="mx-auto w-full max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div
                v-if="blogs.data.length === 0"
                class="rounded-xl border border-dashed border-border py-16 text-center"
            >
                <FileText
                    class="mx-auto mb-4 h-12 w-12 text-muted-foreground"
                    aria-hidden="true"
                />
                <h2 class="text-lg font-semibold text-foreground">
                    No posts yet
                </h2>
                <p class="mt-2 text-sm text-muted-foreground">
                    Blog posts will appear here once developers publish them.
                </p>
            </div>
            <div
                v-else
                class="space-y-8"
            >
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="blog in blogs.data"
                        :key="blog.id"
                        :href="`/blogs/${blog.slug}`"
                        class="block transition-opacity hover:opacity-90"
                    >
                        <Card class="h-full overflow-hidden transition-shadow hover:shadow-md">
                            <div
                                v-if="blog.featured_image_url"
                                class="aspect-video w-full shrink-0 bg-muted"
                            >
                                <img
                                    :src="blog.featured_image_url"
                                    :alt="blog.title"
                                    class="size-full object-cover"
                                />
                            </div>
                            <CardContent class="p-5">
                                <h3 class="font-semibold tracking-tight line-clamp-2">
                                    {{ blog.title }}
                                </h3>
                                <p
                                    v-if="blog.excerpt"
                                    class="mt-2 line-clamp-3 text-sm text-muted-foreground"
                                >
                                    {{ blog.excerpt }}
                                </p>
                                <div class="mt-4 flex items-center gap-2 text-xs text-muted-foreground">
                                    <span>{{ formatDate(blog.published_at) }}</span>
                                    <span v-if="blog.developer" class="flex items-center gap-1">
                                        <User class="size-3.5" />
                                        <Link
                                            :href="`/developers/${blog.developer.slug}`"
                                            class="hover:underline"
                                            @click.stop
                                        >
                                            {{ blog.developer.name }}
                                        </Link>
                                    </span>
                                </div>
                            </CardContent>
                        </Card>
                    </Link>
                </div>
                <Pagination v-if="blogs.links?.length" :links="blogs.links" />
            </div>
        </section>

        <Footer />
    </div>
</template>
