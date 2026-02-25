<script setup lang="ts">
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
    loading?: boolean;
}>();

const emit = defineEmits<{
    (e: 'load-page', url: string): void;
}>();
</script>

<template>
    <section class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div
            v-if="loading"
            class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
        >
            <div
                v-for="i in 6"
                :key="i"
                class="h-64 animate-pulse rounded-lg border border-border bg-muted/50"
            />
        </div>
        <div
            v-else-if="developers.length === 0"
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
            v-if="!loading && meta.last_page > 1"
            class="mt-8 flex items-center justify-center gap-4"
        >
            <button
                v-if="links.prev"
                type="button"
                class="text-sm font-medium text-primary hover:underline disabled:opacity-50"
                :disabled="loading"
                @click="links.prev && emit('load-page', links.prev)"
            >
                Previous
            </button>
            <span class="text-sm text-muted-foreground">
                Page {{ meta.current_page }} of {{ meta.last_page }}
            </span>
            <button
                v-if="links.next"
                type="button"
                class="text-sm font-medium text-primary hover:underline disabled:opacity-50"
                :disabled="loading"
                @click="links.next && emit('load-page', links.next)"
            >
                Next
            </button>
        </div>
    </section>
</template>
