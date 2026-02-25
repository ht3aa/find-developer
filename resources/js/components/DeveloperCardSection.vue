<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import DeveloperCard from '@/components/DeveloperCard.vue';
import type { Developer } from '@/types/developer';

defineProps<{
    developers: Developer[];
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
</script>

<template>
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
</template>
