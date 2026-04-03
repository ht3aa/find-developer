<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { BookOpen, Bug, ChevronUp, CircleHelp, X } from 'lucide-vue-next';
import {
    computed,
    defineAsyncComponent,
    onMounted,
    onUnmounted,
    ref,
} from 'vue';
import Footer from '@/components/Footer.vue';
import Hero from '@/components/Hero.vue';
import Navbar from '@/components/Navbar.vue';
import SeoHead from '@/components/SeoHead.vue';
import { Button } from '@/components/ui/button';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import {
    destroyWelcomeTour,
    startWelcomeTour,
} from '@/composables/useWelcomeTour';
import remoteWorkRoutes from '@/routes/remote-work';

const DeveloperCardSection = defineAsyncComponent(
    () => import('@/components/DeveloperCardSection.vue'),
);

const page = usePage();
const flashSuccess = computed(
    () => (page.props.flash as { success?: string })?.success,
);
const appUrl = computed(() => (page.props.appUrl as string) ?? '');
const appOgImage = computed(
    () => (page.props.appOgImage as string) ?? undefined,
);
const newsletterStoreUrl = computed(
    () => (page.props.newsletterStoreUrl as string) ?? '',
);
const developerOffersStoreUrl = computed(
    () => (page.props.developerOffersStoreUrl as string) ?? null,
);
const heroGreetingNote = computed(
    () => (page.props.heroGreetingNote as string | undefined) ?? '',
);
const webSiteJsonLd = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'WebSite',
    name: 'Find Developer',
    description:
        'Find the right developer for your project. Browse vetted developers, filter by skills and experience.',
    ...(appUrl.value ? { url: appUrl.value } : {}),
}));

const reportBugsUrl = 'https://github.com/ht3aa/find-developer';

/** Surah 110 — An-Nasr (three ayahs). */
const surah110TitleAr = 'سورة النصر (١١٠)';

const surah110Ayahs = [
    'إِذَا جَاءَ نَصْرُ اللَّهِ وَالْفَتْحُ',
    'وَرَأَيْتَ النَّاسَ يَدْخُلُونَ فِي دِينِ اللَّهِ أَفْوَاجًا',
    'فَسَبِّحْ بِحَمْدِ رَبِّكَ وَاسْتَغْفِرْهُ ۚ إِنَّهُ كَانَ تَوَّابًا',
] as const;

const WELCOME_HELP_NUDGE_KEY = 'find-developer-welcome-help-nudge-dismissed';

const showBackToTop = ref(false);
const showHelpNudge = ref(false);
let helpNudgeTimerId: ReturnType<typeof setTimeout> | null = null;

function dismissHelpNudge(persist = true): void {
    showHelpNudge.value = false;
    if (persist && typeof localStorage !== 'undefined') {
        localStorage.setItem(WELCOME_HELP_NUDGE_KEY, '1');
    }
}

const handleScroll = () => {
    showBackToTop.value = window.scrollY > 300;
};

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

onMounted(() => {
    window.scrollTo(0, 0);
    window.addEventListener('scroll', handleScroll, { passive: true });
    if (
        typeof localStorage !== 'undefined' &&
        !localStorage.getItem(WELCOME_HELP_NUDGE_KEY)
    ) {
        helpNudgeTimerId = window.setTimeout(() => {
            showHelpNudge.value = true;
        }, 1400);
    }
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    if (helpNudgeTimerId !== null) {
        window.clearTimeout(helpNudgeTimerId);
    }
    destroyWelcomeTour();
});

async function runWelcomeTour(): Promise<void> {
    dismissHelpNudge(true);
    await startWelcomeTour();
}
</script>

<template>
    <SeoHead
        title="Find the Right Developer for Your Project"
        description="Connect with vetted developers for your project. Browse portfolios, filter by skills and experience, and hire the best match for your team."
        canonical="/"
        :image="appOgImage"
        :json-ld="webSiteJsonLd"
    />
    <Head />
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <div
            class="border-b border-border bg-muted/50 px-4 py-2 text-center text-sm text-muted-foreground"
            role="banner"
        >
            <span class="inline-flex items-center justify-center gap-1.5">
                <Bug class="size-4 shrink-0" aria-hidden />
                If you find any bug or error, please
                <a
                    :href="reportBugsUrl"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="font-medium text-foreground underline underline-offset-2 transition-colors hover:text-primary"
                >
                    report it on GitHub
                </a>
            </span>
        </div>

        <div
            class="border-b border-border bg-muted/50 px-4 py-2 text-center text-xs text-muted-foreground"
            role="region"
            aria-labelledby="surah-110-heading"
        >
            <div
                class="mx-auto inline-flex max-w-full min-w-0 flex-col items-center gap-1"
            >
                <h2
                    id="surah-110-heading"
                    class="inline-flex items-center justify-center gap-1 font-medium text-foreground"
                    dir="rtl"
                    lang="ar"
                >
                    {{ surah110TitleAr }}
                    <BookOpen class="size-3 shrink-0" aria-hidden />
                </h2>
                <div class="w-full min-w-0 overflow-x-auto" dir="rtl" lang="ar">
                    <p
                        class="inline-flex min-w-min flex-nowrap items-baseline justify-center gap-x-2 leading-snug font-medium whitespace-nowrap text-foreground"
                    >
                        <span
                            v-for="(ayah, index) in surah110Ayahs"
                            :key="index"
                            class="inline-flex items-baseline gap-0.5"
                        >
                            <span class="text-muted-foreground tabular-nums"
                                >{{ index + 1 }}.</span
                            >{{ ayah }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div data-tour="welcome-hero">
            <Hero
                :hero-greeting-note="heroGreetingNote || undefined"
                badge="Find developers"
                title="Find the right developer for your project"
                description="Browse vetted developers, filter by skills and experience, and connect with the best match for your team."
                primary-action-label="Browse developers"
                primary-action-href="#developers"
                secondary-action-label="Remote work"
                :secondary-action-href="remoteWorkRoutes.index.url()"
                :success-message="flashSuccess ?? undefined"
                :newsletter-store-url="newsletterStoreUrl || undefined"
            />
        </div>

        <Suspense>
            <DeveloperCardSection
                :developer-offers-store-url="
                    developerOffersStoreUrl ?? undefined
                "
            />
            <template #fallback>
                <section
                    id="developers"
                    class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8"
                >
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="i in 6"
                            :key="i"
                            class="h-64 animate-pulse rounded-lg border border-border bg-muted/50"
                        />
                    </div>
                </section>
            </template>
        </Suspense>

        <Footer />

        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-1"
        >
            <div
                v-if="showHelpNudge"
                class="fixed z-[60] w-[min(19rem,calc(100vw-3rem))] rounded-lg border border-border bg-popover p-3 text-popover-foreground shadow-lg outline-none"
                role="dialog"
                aria-labelledby="welcome-help-nudge-title"
                aria-describedby="welcome-help-nudge-desc"
                :style="{ left: '1.5rem', bottom: '5.25rem' }"
            >
                <div class="flex gap-2">
                    <div class="min-w-0 flex-1">
                        <p
                            id="welcome-help-nudge-title"
                            class="text-sm font-semibold text-foreground"
                        >
                            Need a hand?
                        </p>
                        <p
                            id="welcome-help-nudge-desc"
                            class="mt-1 text-xs leading-relaxed text-muted-foreground"
                        >
                            Click me if you need any help — the button below
                            starts a short tour of how to search and explore
                            developers.
                        </p>
                    </div>
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        class="size-8 shrink-0 text-muted-foreground hover:text-foreground"
                        aria-label="Dismiss help hint"
                        @click="dismissHelpNudge(true)"
                    >
                        <X class="size-4" aria-hidden="true" />
                    </Button>
                </div>
                <div
                    class="pointer-events-none absolute -bottom-1.5 left-7 size-3 rotate-45 border-r border-b border-border bg-popover"
                    aria-hidden="true"
                />
            </div>
        </Transition>

        <TooltipProvider :delay-duration="250">
            <Tooltip>
                <TooltipTrigger as-child>
                    <button
                        type="button"
                        class="fixed bottom-6 left-6 z-50 flex size-12 items-center justify-center rounded-full border border-border bg-card text-foreground shadow-lg transition-all hover:scale-105 hover:bg-muted/80 focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:outline-none"
                        aria-label="Click me if you need any help — starts a guided tour of this page"
                        @click="runWelcomeTour"
                    >
                        <CircleHelp
                            class="size-6 text-primary"
                            aria-hidden="true"
                        />
                    </button>
                </TooltipTrigger>
                <TooltipContent
                    side="right"
                    :side-offset="10"
                    class="max-w-[16rem] text-balance"
                >
                    Click me if you need any help
                </TooltipContent>
            </Tooltip>
        </TooltipProvider>

        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <button
                v-if="showBackToTop"
                class="fixed right-6 bottom-6 z-40 flex size-10 items-center justify-center rounded-full bg-primary text-primary-foreground shadow-lg transition-all hover:scale-110 hover:bg-primary/90"
                aria-label="Back to top"
                @click="scrollToTop"
            >
                <ChevronUp class="size-5" />
            </button>
        </Transition>
    </div>
</template>
