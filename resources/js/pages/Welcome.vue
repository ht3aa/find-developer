<script setup lang="ts">
import { refDebounced } from '@vueuse/core';
import { Head } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import Navbar from '@/components/Navbar.vue';
import { Input } from '@/components/ui/input';

defineProps<{
    canRegister?: boolean;
}>();

const searchQuery = ref('');
const debouncedQuery = refDebounced(searchQuery, 300);

watch(debouncedQuery, (query) => {
    if (query === '') return;
    // TODO: run live search, e.g. fetch results or navigate
});
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
    </div>
</template>
