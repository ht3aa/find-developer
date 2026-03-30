<script setup lang="ts">
import { ChevronLeft, ChevronRight, Package } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const props = withDefaults(
    defineProps<{
        /** Resolved image URLs (e.g. signed S3 URLs). */
        images: string[];
        alt: string;
        aspectClass?: string;
        /** Larger controls and dots on the product page. */
        variant?: 'card' | 'detail';
    }>(),
    {
        aspectClass: 'aspect-[4/3]',
        variant: 'card',
    },
);

const index = ref(0);

const count = computed(() => props.images.length);

const currentSrc = computed(() => props.images[index.value] ?? null);

watch(
    () => props.images,
    () => {
        index.value = 0;
    },
    { deep: true },
);

function go(delta: number, event?: Event): void {
    event?.preventDefault();
    event?.stopPropagation();
    if (count.value === 0) {
        return;
    }
    index.value = (index.value + delta + count.value) % count.value;
}

const isDetail = computed(() => props.variant === 'detail');
</script>

<template>
    <div :class="[aspectClass, 'relative w-full overflow-hidden bg-muted']">
        <template v-if="count === 0">
            <div
                class="flex size-full items-center justify-center text-muted-foreground"
            >
                <Package
                    :class="
                        isDetail ? 'size-20 opacity-40' : 'size-12 opacity-40'
                    "
                />
            </div>
        </template>
        <template v-else>
            <img
                v-if="currentSrc"
                :src="currentSrc"
                :alt="alt"
                :class="
                    isDetail
                        ? 'size-full object-contain object-center'
                        : 'size-full object-cover'
                "
                loading="lazy"
            />

            <div
                v-if="count > 1"
                class="pointer-events-none absolute inset-0 z-10 flex items-center justify-between px-1"
            >
                <button
                    type="button"
                    class="pointer-events-auto inline-flex size-8 items-center justify-center rounded-full border border-border/80 bg-background/90 text-foreground shadow-sm backdrop-blur-sm transition hover:bg-background disabled:opacity-40 sm:size-9"
                    :class="{ 'size-10 sm:size-11': isDetail }"
                    aria-label="Previous image"
                    @click="go(-1, $event)"
                >
                    <ChevronLeft class="size-4 shrink-0 sm:size-5" />
                </button>
                <button
                    type="button"
                    class="pointer-events-auto inline-flex size-8 items-center justify-center rounded-full border border-border/80 bg-background/90 text-foreground shadow-sm backdrop-blur-sm transition hover:bg-background disabled:opacity-40 sm:size-9"
                    :class="{ 'size-10 sm:size-11': isDetail }"
                    aria-label="Next image"
                    @click="go(1, $event)"
                >
                    <ChevronRight class="size-4 shrink-0 sm:size-5" />
                </button>
            </div>

            <div
                v-if="count > 1"
                class="pointer-events-none absolute right-0 bottom-2 left-0 z-10 flex justify-center gap-1.5"
            >
                <span
                    v-for="(_, i) in images"
                    :key="i"
                    class="rounded-full transition-all"
                    :class="
                        i === index
                            ? 'h-1.5 w-5 bg-primary'
                            : 'h-1.5 w-1.5 bg-muted-foreground/50'
                    "
                    aria-hidden="true"
                />
            </div>
        </template>
    </div>
</template>
