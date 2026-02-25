<script setup lang="ts">
import { refDebounced } from '@vueuse/core';
import { Head, Link, router } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import DeveloperCard from '@/components/DeveloperCard.vue';
import Navbar from '@/components/Navbar.vue';
import { Input } from '@/components/ui/input';
import type { Developer } from '@/types/developer';

const props = defineProps<{
    canRegister?: boolean;
    developers: Developer[];
    filterSearch?: string | null;
    meta: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number | null;
        to: number | null;
    };
    links: {
        prev: string | null;
        next: string | null;
    };
}>();

const searchQuery = ref(props.filterSearch ?? '');
const debouncedQuery = refDebounced(searchQuery, 300);

function homeUrl(query: string): string {
    if (!query) return '/';
    const params = new URLSearchParams({ 'filter[search]': query });
    return `/?${params.toString()}`;
}

watch(debouncedQuery, (query: string) => {
    router.get(homeUrl(query), {}, { preserveState: true, replace: true });
}, { immediate: false });
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <!-- Hero section -->
        <section
            class="flex w-full flex-col items-center justify-center px-4 py-16 sm:py-20 lg:py-28"
        >
            <div class="w-full max-w-3xl text-center">
                <div
                    class="relative flex w-full items-center rounded-lg border border-border bg-card shadow-sm transition-colors focus-within:border-ring focus-within:ring-2 focus-within:ring-ring/20"
                >
                    <Search
                        class="pointer-events-none absolute left-4 h-5 w-5 shrink-0 text-muted-foreground"
                        aria-hidden="true"
                    />
                    <Input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Search developers by skill, e.g. Laravel, Vue, React..."
                        class="h-12 w-full border-0 bg-transparent pl-12 pr-4 text-base shadow-none placeholder:text-muted-foreground focus-visible:ring-0 sm:h-14 sm:pl-14 sm:text-lg"
                        autocomplete="off"
                    />
                </div>
            </div>
        </section>

        <!-- Developers section -->
        <section class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div
                v-if="developers.length === 0"
                class="rounded-lg border border-dashed border-border py-12 text-center text-muted-foreground"
            >
                No developers found.
            </div>
            <div
                v-else
                class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
            >
                <DeveloperCard
                    v-for="developer in developers"
                    :key="developer.id"
                    :developer="developer"
                />
            </div>

            <div
                v-if="meta.last_page > 1"
                class="mt-8 flex items-center justify-center gap-4"
            >
                <Link
                    v-if="links.prev"
                    :href="links.prev"
                    class="text-sm font-medium text-primary hover:underline"
                >
                    Previous
                </Link>
                <span class="text-sm text-muted-foreground">
                    Page {{ meta.current_page }} of {{ meta.last_page }}
                </span>
                <Link
                    v-if="links.next"
                    :href="links.next"
                    class="text-sm font-medium text-primary hover:underline"
                >
                    Next
                </Link>
            </div>
        </section>
    </div>
</template>
