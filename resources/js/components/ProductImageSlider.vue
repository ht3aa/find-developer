<script setup lang="ts">
import { ChevronLeft, ChevronRight, Package } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

const AUTOPLAY_INTERVAL_MS = 5_000;

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

const isPointerPaused = ref(false);
const isTabHidden = ref(false);
const prefersReducedMotion = ref(false);

const autoplayPaused = computed(
    () =>
        isPointerPaused.value ||
        isTabHidden.value ||
        prefersReducedMotion.value,
);

let autoplayTimerId: ReturnType<typeof setInterval> | null = null;
let reducedMotionMql: MediaQueryList | null = null;

function syncReducedMotion(): void {
    prefersReducedMotion.value =
        typeof window !== 'undefined' &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

if (typeof window !== 'undefined') {
    syncReducedMotion();
}

function clearAutoplay(): void {
    if (autoplayTimerId !== null) {
        clearInterval(autoplayTimerId);
        autoplayTimerId = null;
    }
}

function startAutoplay(): void {
    clearAutoplay();
    if (count.value <= 1 || prefersReducedMotion.value) {
        return;
    }
    autoplayTimerId = setInterval(() => {
        if (autoplayPaused.value || count.value <= 1) {
            return;
        }
        index.value = (index.value + 1) % count.value;
    }, AUTOPLAY_INTERVAL_MS);
}

function onVisibilityChange(): void {
    isTabHidden.value =
        typeof document !== 'undefined' && document.visibilityState === 'hidden';
}

watch(
    () => props.images,
    () => {
        index.value = 0;
    },
    { deep: true },
);

watch(
    [count, prefersReducedMotion],
    () => {
        clearAutoplay();
        startAutoplay();
    },
    { immediate: true, flush: 'post' },
);

onMounted(() => {
    syncReducedMotion();
    reducedMotionMql = window.matchMedia('(prefers-reduced-motion: reduce)');
    reducedMotionMql.addEventListener('change', syncReducedMotion);
    isTabHidden.value = document.visibilityState === 'hidden';
    document.addEventListener('visibilitychange', onVisibilityChange);
});

onUnmounted(() => {
    reducedMotionMql?.removeEventListener('change', syncReducedMotion);
    document.removeEventListener('visibilitychange', onVisibilityChange);
    clearAutoplay();
});

function go(delta: number, event?: Event): void {
    event?.preventDefault();
    event?.stopPropagation();
    if (count.value === 0) {
        return;
    }
    index.value = (index.value + delta + count.value) % count.value;
}

function goTo(i: number, event?: Event): void {
    event?.preventDefault();
    event?.stopPropagation();
    if (count.value === 0 || i < 0 || i >= count.value) {
        return;
    }
    index.value = i;
}

const trackTransform = computed(() => {
    if (count.value === 0) {
        return 'translateX(0)';
    }
    const pct = (index.value * 100) / count.value;
    return `translateX(-${pct}%)`;
});

const touchStartX = ref(0);

function onTouchStart(e: TouchEvent): void {
    if (count.value <= 1) {
        return;
    }
    touchStartX.value = e.touches[0]?.clientX ?? 0;
}

function onTouchEnd(e: TouchEvent): void {
    if (count.value <= 1) {
        return;
    }
    const endX = e.changedTouches[0]?.clientX ?? touchStartX.value;
    const dx = endX - touchStartX.value;
    const threshold = 48;
    if (dx > threshold) {
        go(-1);
    } else if (dx < -threshold) {
        go(1);
    }
}

const isDetail = computed(() => props.variant === 'detail');
</script>

<template>
    <div
        :class="[aspectClass, 'relative w-full overflow-hidden bg-muted']"
        @touchstart.passive="onTouchStart"
        @touchend="onTouchEnd"
        @mouseenter="isPointerPaused = true"
        @mouseleave="isPointerPaused = false"
        @focusin="isPointerPaused = true"
        @focusout="isPointerPaused = false"
    >
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
            <div
                class="absolute inset-0 overflow-hidden"
                role="region"
                :aria-label="`${alt} — image ${index + 1} of ${count}`"
            >
                <div
                    class="flex h-full ease-out motion-safe:transition-transform motion-safe:duration-300 motion-reduce:transition-none"
                    :style="{
                        width: `${count * 100}%`,
                        transform: trackTransform,
                    }"
                >
                    <div
                        v-for="(src, i) in images"
                        :key="`${src}-${i}`"
                        class="flex h-full shrink-0 items-center justify-center"
                        :style="{ width: `${100 / count}%` }"
                    >
                        <img
                            :src="src"
                            :alt="i === 0 ? alt : `${alt} (${i + 1} of ${count})`"
                            :class="
                                isDetail
                                    ? 'max-h-full max-w-full object-contain object-center'
                                    : 'size-full object-cover'
                            "
                            :loading="i === 0 ? 'eager' : 'lazy'"
                            draggable="false"
                        />
                    </div>
                </div>
            </div>

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
                <button
                    v-for="(_, i) in images"
                    :key="i"
                    type="button"
                    class="pointer-events-auto rounded-full transition-all focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:outline-none"
                    :class="
                        i === index
                            ? 'h-1.5 w-5 bg-primary'
                            : 'h-1.5 w-1.5 bg-muted-foreground/50 hover:bg-muted-foreground/70'
                    "
                    :aria-label="`Go to image ${i + 1}`"
                    :aria-current="i === index ? 'true' : undefined"
                    @click="goTo(i, $event)"
                />
            </div>
        </template>
    </div>
</template>
