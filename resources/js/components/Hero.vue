<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { ArrowDown, Loader2 } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import DotGrid from '@/components/animations/DotGrid.vue';
import Duck from '@/components/Duck.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

const QURAN_API_BASE = 'https://quranapi.pages.dev/api';
const VERSE_REFRESH_INTERVAL_MS = 60_000;

const ARABIC_NUMERALS = '٠١٢٣٤٥٦٧٨٩';

function toArabicNumerals(n: number): string {
    return String(n).replace(/[0-9]/g, (d) => ARABIC_NUMERALS[Number(d)] ?? d);
}

interface QuranVerse {
    surahNo: number;
    ayahNo: number;
    surahNameArabicLong: string;
    surahNameTranslation: string;
    arabic1: string;
    english: string;
}

const props = withDefaults(
    defineProps<{
        badge?: string;
        title?: string;
        description?: string;
        primaryActionLabel?: string;
        primaryActionHref?: string;
        successMessage?: string;
        /** When set, shows the newsletter signup field in the hero. */
        newsletterStoreUrl?: string;
        /** YouTube video URL (e.g. https://www.youtube.com/watch?v=VIDEO_ID or https://youtu.be/VIDEO_ID). */
        youtubeUrl?: string;
    }>(),
    {
        badge: 'Find developers',
        title: 'Find the right developer for your project',
        description:
            'Browse vetted developers, filter by skills and experience, and connect with the best match for your team.',
    },
);

const page = usePage();
const errors = computed(
    () => (page.props.errors as Record<string, string> | undefined) ?? {},
);

function getYoutubeVideoId(url: string | undefined): string | null {
    if (!url?.trim()) return null;
    const trimmed = url.trim();
    const watchMatch = trimmed.match(
        /(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/,
    );
    const embedMatch = trimmed.match(
        /youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/,
    );
    const videoId = watchMatch?.[1] ?? embedMatch?.[1];
    return videoId;
}

const youtubeVideoId = computed(() => getYoutubeVideoId(props.youtubeUrl));
const email = ref('');
const submitting = ref(false);
const verse = ref<QuranVerse | null>(null);
let verseRefreshTimerId: ReturnType<typeof setInterval> | null = null;

async function fetchRandomVerse(): Promise<QuranVerse | null> {
    const maxSurah = 114;
    const maxAyah = 25;
    const maxAttempts = 20;

    for (let attempt = 0; attempt < maxAttempts; attempt++) {
        const surah = Math.floor(Math.random() * maxSurah) + 1;
        const ayah = Math.floor(Math.random() * maxAyah) + 1;
        const url = `${QURAN_API_BASE}/${surah}/${ayah}.json`;

        try {
            const res = await fetch(url);
            if (!res.ok) continue;

            const data = (await res.json()) as QuranVerse & Record<string, unknown>;
            if (
                data.surahNo != null &&
                data.ayahNo != null &&
                data.surahNameArabicLong &&
                data.surahNameTranslation &&
                data.arabic1 &&
                data.english
            ) {
                return {
                    surahNo: data.surahNo,
                    ayahNo: data.ayahNo,
                    surahNameArabicLong: data.surahNameArabicLong,
                    surahNameTranslation: data.surahNameTranslation,
                    arabic1: data.arabic1,
                    english: data.english,
                };
            }
        } catch {
            // Retry with different numbers
        }
    }

    return null;
}

async function loadVerse(): Promise<void> {
    const result = await fetchRandomVerse();
    if (result) {
        verse.value = result;
    }
}

function startVerseRefreshInterval(): void {
    verseRefreshTimerId = setInterval(loadVerse, VERSE_REFRESH_INTERVAL_MS);
}

function scrollToSearch(): void {
    const target = props.primaryActionHref
        ? document.querySelector(props.primaryActionHref)
        : null;
    if (target) {
        target.scrollIntoView({ behavior: 'smooth' });
    } else {
        window.scrollBy({ top: window.innerHeight, behavior: 'smooth' });
    }
}

function submitNewsletter(): void {
    if (!props.newsletterStoreUrl || !email.value.trim()) return;
    submitting.value = true;
    router.post(
        props.newsletterStoreUrl,
        { email: email.value.trim() },
        {
            preserveScroll: true,
            onFinish: () => {
                submitting.value = false;
            },
        },
    );
}

onMounted(() => {
    loadVerse().then(() => {
        startVerseRefreshInterval();
    });
});

onUnmounted(() => {
    if (verseRefreshTimerId) {
        clearInterval(verseRefreshTimerId);
    }
});
</script>

<template>
    <section
        class="relative overflow-hidden border-b border-border bg-gradient-to-b from-background via-muted/30 to-background"
    >
        <!-- Decorative background elements -->
        <div
            class="pointer-events-none absolute inset-0 overflow-hidden"
            aria-hidden="true"
        >
            <DotGrid
                :dot-size="3"
                :gap="100"
                base-color="#C4C2BC"
                active-color="#F59E0B"
                :proximity="140"
                :speed-trigger="80"
                :shock-radius="220"
                :shock-strength="4"
                :max-speed="4000"
                :resistance="800"
                :return-duration="1.8"
                class-name="custom-dot-grid"
            />
            <div
                class="absolute -top-40 -left-40 size-80 rounded-full bg-primary/10 blur-3xl"
            />
            <div
                class="absolute top-1/2 -right-40 size-96 -translate-y-1/2 rounded-full bg-secondary/10 blur-3xl"
            />
            <div
                class="absolute bottom-0 left-1/2 size-[500px] -translate-x-1/2 rounded-full bg-primary/5 blur-3xl"
            />
        </div>

        <!-- Decorative ducks (click to quack) -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div
                class="pointer-events-auto absolute top-[15%] left-[8%] rotate-[-15deg]"
            >
                <div class="hero-duck hero-duck-1">
                    <Duck size="size-14" class="opacity-90" />
                </div>
            </div>
            <div
                class="pointer-events-auto absolute top-[25%] right-[12%] rotate-[20deg]"
            >
                <div class="hero-duck hero-duck-2">
                    <Duck size="size-10" class="opacity-85" />
                </div>
            </div>
            <div
                class="pointer-events-auto absolute bottom-[20%] left-[15%] rotate-[10deg]"
            >
                <div class="hero-duck hero-duck-3">
                    <Duck size="size-12" class="opacity-80" />
                </div>
            </div>
            <div
                class="pointer-events-auto absolute right-[8%] bottom-[25%] rotate-[-20deg]"
            >
                <div class="hero-duck hero-duck-4">
                    <Duck size="size-11" class="opacity-85" />
                </div>
            </div>
        </div>

        <div
            class="relative mx-auto max-w-5xl px-4 py-20 sm:px-6 sm:py-24 lg:px-8 lg:py-32"
        >
            <div class="flex flex-col items-center text-center">
                <!-- Success message -->
                <div
                    v-if="props.successMessage"
                    class="mb-6 w-full max-w-2xl rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:border-green-800 dark:bg-green-950/50 dark:text-green-200"
                >
                    {{ props.successMessage }}
                </div>
                <!-- Badge -->
                <div
                    v-if="props.badge != null"
                    class="mb-6 flex items-center justify-center gap-2"
                >
                    <Duck size="size-8" class="shrink-0 rotate-[-12deg]" />
                    <Badge
                        variant="secondary"
                        class="rounded-full border border-primary/20 bg-primary/5 px-4 py-1.5 text-sm font-medium text-primary shadow-sm"
                    >
                        {{ props.badge }}
                    </Badge>
                    <Duck size="size-8" class="shrink-0 rotate-[12deg]" />
                </div>

                <!-- Title -->
                <h1
                    v-if="props.title != null"
                    class="text-3xl font-bold tracking-tight sm:text-4xl md:text-5xl lg:text-6xl"
                >
                    {{ props.title }}
                </h1>
                <p
                    v-if="props.description != null"
                    class="mt-4 max-w-2xl text-base text-muted-foreground sm:text-lg md:text-xl"
                >
                    {{ props.description }}
                </p>

                <!-- CTA button -->
                <Button
                    v-if="props.primaryActionLabel != null"
                    size="lg"
                    class="mt-10 gap-2 rounded-xl text-base shadow-lg transition-all duration-200 hover:shadow-xl"
                    @click="scrollToSearch"
                >
                    {{ props.primaryActionLabel }}
                    <ArrowDown class="size-4" aria-hidden="true" />
                </Button>

                <!-- Newsletter signup -->
                <div
                    v-if="props.newsletterStoreUrl"
                    class="mt-12 w-full max-w-md"
                >
                    <p class="mb-3 text-sm font-medium text-muted-foreground">
                        Get developers spotlight in your inbox
                    </p>
                    <form
                        class="flex flex-col gap-2 sm:flex-row sm:items-center"
                        @submit.prevent="submitNewsletter"
                    >
                        <Input
                            v-model="email"
                            type="email"
                            name="email"
                            placeholder="you@example.com"
                            autocomplete="email"
                            class="flex-1"
                            :disabled="submitting"
                            :aria-invalid="!!errors?.email"
                            :aria-describedby="
                                errors?.email
                                    ? 'newsletter-email-error'
                                    : undefined
                            "
                        />
                        <Button
                            type="submit"
                            class="shrink-0"
                            :disabled="submitting || !email.trim()"
                        >
                            <Loader2
                                v-if="submitting"
                                class="size-4 animate-spin"
                                aria-hidden
                            />
                            <span>{{
                                submitting ? 'Subscribing…' : 'Subscribe'
                            }}</span>
                        </Button>
                    </form>
                    <p
                        v-if="errors.email"
                        id="newsletter-email-error"
                        class="mt-2 text-sm text-destructive"
                        role="alert"
                    >
                        {{ errors.email }}
                    </p>
                </div>

                <!-- Scroll indicator -->
                <button
                    type="button"
                    class="scroll-indicator mt-12 flex flex-col items-center gap-1 rounded-full p-2 text-muted-foreground/70 transition-colors hover:text-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                    aria-label="Scroll down to see more"
                    @click="scrollToSearch"
                >
                    <span class="text-xs font-medium">Scroll</span>
                    <ArrowDown class="scroll-arrow size-5" aria-hidden="true" />
                </button>

                <!-- YouTube video -->
                <div
                    v-if="youtubeVideoId"
                    class="mt-12 w-full max-w-3xl overflow-hidden rounded-xl border border-border shadow-lg"
                >
                    <iframe
                        :src="`https://www.youtube.com/embed/${youtubeVideoId}?autoplay=1&mute=1&loop=1&playlist=${youtubeVideoId}`"
                        title="YouTube video"
                        class="aspect-video w-full"
                        allow="
                            accelerometer;
                            autoplay;
                            clipboard-write;
                            encrypted-media;
                            gyroscope;
                            picture-in-picture;
                        "
                        allowfullscreen
                    />
                </div>

                <!-- Quran verse -->
                <div
                    v-if="verse"
                    class="mt-12 w-full max-w-2xl rounded-lg border border-border/60 bg-muted/30 px-4 py-3 text-center"
                >
                    <p class="text-xs font-medium text-muted-foreground">
                        <span dir="rtl">
                            {{ verse.surahNameArabicLong }}
                            (سورة {{ toArabicNumerals(verse.surahNo) }}، آية {{ toArabicNumerals(verse.ayahNo) }})
                        </span>
                        — ({{ verse.surahNameTranslation }}) Surah {{ verse.surahNo }}, Ayah {{ verse.ayahNo }}
                    </p>
                    <p
                        class="mt-2 text-lg font-medium leading-relaxed text-foreground"
                        dir="rtl"
                    >
                        {{ verse.arabic1 }}
                    </p>
                    <p class="mt-1 text-sm italic text-muted-foreground">
                        {{ verse.english }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Bottom accent gradient -->
        <div
            class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-transparent via-primary/60 to-transparent opacity-80"
        />
    </section>
</template>

<style scoped>
@keyframes float {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.hero-duck {
    animation: float 3s ease-in-out infinite;
}

.hero-duck-1 {
    animation-duration: 2.8s;
    animation-delay: 0s;
}

.hero-duck-2 {
    animation-duration: 3.2s;
    animation-delay: 0.4s;
}

.hero-duck-3 {
    animation-duration: 3s;
    animation-delay: 0.7s;
}

.hero-duck-4 {
    animation-duration: 2.6s;
    animation-delay: 0.2s;
}

@keyframes scroll-bounce {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(6px);
    }
}

.scroll-arrow {
    animation: scroll-bounce 2s ease-in-out infinite;
}
</style>
