<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
import { Bug } from 'lucide-vue-next';
import DeveloperCardSection from '@/components/DeveloperCardSection.vue';
import Footer from '@/components/Footer.vue';
import Hero from '@/components/Hero.vue';
import Navbar from '@/components/Navbar.vue';

const page = usePage();
const flashSuccess = computed(() => (page.props.flash as { success?: string })?.success);

const reportBugsUrl = 'https://github.com/ht3aa/find-developer';

onMounted(() => {
    window.scrollTo(0, 0);
});
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
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

        <Hero
            badge="Find developers"
            title="Find the right developer for your project"
            description="Browse vetted developers, filter by skills and experience, and connect with the best match for your team."
            primary-action-label="Browse developers"
            primary-action-href="#developers"
            :success-message="flashSuccess ?? undefined"
        />

        <DeveloperCardSection />

        <Footer />
    </div>
</template>
