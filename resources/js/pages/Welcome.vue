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
    <div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a]">
        <Navbar />

        <!-- Hero section -->
        <section
            class="flex w-full flex-col items-center justify-center px-4 py-16 sm:py-20 lg:py-28"
        >
            <div class="w-full max-w-3xl text-center">
                <div
                    class="relative flex w-full items-center rounded-lg border border-[#e3e3e0] bg-white shadow-sm transition-colors focus-within:border-[#1b1b18] focus-within:ring-2 focus-within:ring-[#1b1b18]/20 dark:border-[#3E3E3A] dark:bg-[#161615] dark:focus-within:border-[#EDEDEC] dark:focus-within:ring-[#EDEDEC]/20"
                >
                    <Search
                        class="pointer-events-none absolute left-4 h-5 w-5 shrink-0 text-[#706f6c] dark:text-[#A1A09A]"
                        aria-hidden="true"
                    />
                    <Input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Search developers by skill, e.g. Laravel, Vue, React..."
                        class="h-12 w-full border-0 bg-transparent pl-12 pr-4 text-base shadow-none placeholder:text-[#706f6c] focus-visible:ring-0 dark:placeholder:text-[#A1A09A] sm:h-14 sm:pl-14 sm:text-lg"
                        autocomplete="off"
                    />
                </div>
            </div>
        </section>

        <!-- Developers section -->
        <section class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div
                v-if="developers.length === 0"
                class="rounded-lg border border-dashed py-12 text-center text-[#706f6c] dark:text-[#A1A09A]"
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
                    class="text-sm font-medium text-[#1b1b18] hover:underline dark:text-[#EDEDEC]"
                >
                    Previous
                </Link>
                <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                    Page {{ meta.current_page }} of {{ meta.last_page }}
                </span>
                <Link
                    v-if="links.next"
                    :href="links.next"
                    class="text-sm font-medium text-[#1b1b18] hover:underline dark:text-[#EDEDEC]"
                >
                    Next
                </Link>
            </div>
        </section>
    </div>
</template>
