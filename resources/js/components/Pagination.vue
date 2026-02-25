<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

defineProps<{
    links: PaginationLink[];
}>();
</script>

<template>
    <nav
        v-if="links.length > 1"
        class="flex items-center justify-center gap-2"
        aria-label="Pagination"
    >
        <template v-for="(link, index) in links" :key="index">
            <Link
                v-if="link.url"
                :href="link.url"
                :class="[
                    'inline-flex items-center justify-center rounded-md px-3 py-1.5 text-sm font-medium transition-colors',
                    link.active
                        ? 'bg-primary text-primary-foreground'
                        : 'text-muted-foreground hover:bg-muted hover:text-foreground',
                ]"
                :aria-current="link.active ? 'page' : undefined"
                preserve-scroll
            >
                <ChevronLeft v-if="link.label === '&laquo; Previous'" class="size-4" />
                <ChevronRight v-else-if="link.label === 'Next &raquo;'" class="size-4" />
                <span v-else v-html="link.label" />
            </Link>
            <span
                v-else
                :class="[
                    'inline-flex cursor-not-allowed items-center justify-center rounded-md px-3 py-1.5 text-sm font-medium text-muted-foreground/50',
                ]"
                aria-disabled="true"
            >
                <ChevronLeft v-if="link.label === '&laquo; Previous'" class="size-4" />
                <ChevronRight v-else-if="link.label === 'Next &raquo;'" class="size-4" />
                <span v-else v-html="link.label" />
            </span>
        </template>
    </nav>
</template>
