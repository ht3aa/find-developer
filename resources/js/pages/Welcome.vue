<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { Bug, ChevronUp, CircleHelp } from 'lucide-vue-next';
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
import {
    destroyWelcomeTour,
    startWelcomeTour,
} from '@/composables/useWelcomeTour';

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

const showBackToTop = ref(false);

const handleScroll = () => {
    showBackToTop.value = window.scrollY > 300;
};

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

onMounted(() => {
    window.scrollTo(0, 0);
    window.addEventListener('scroll', handleScroll, { passive: true });
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    destroyWelcomeTour();
});

async function runWelcomeTour(): Promise<void> {
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

        <div data-tour="welcome-hero">
            <Hero
                :hero-greeting-note="heroGreetingNote || undefined"
                badge="Find developers"
                title="Find the right developer for your project"
                description="Browse vetted developers, filter by skills and experience, and connect with the best match for your team."
                primary-action-label="Browse developers"
                primary-action-href="#developers"
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

        <button
            type="button"
            class="fixed left-6 bottom-6 z-50 flex size-12 items-center justify-center rounded-full border border-border bg-card text-foreground shadow-lg transition-all hover:scale-105 hover:bg-muted/80 focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:outline-none"
            aria-label="Start page tour: how to search developers"
            title="How to use this page"
            @click="runWelcomeTour"
        >
            <CircleHelp class="size-6 text-primary" aria-hidden="true" />
        </button>

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
