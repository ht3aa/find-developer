<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
import DeveloperCardSection from '@/components/DeveloperCardSection.vue';
import Footer from '@/components/Footer.vue';
import Hero from '@/components/Hero.vue';
import Navbar from '@/components/Navbar.vue';

const page = usePage();
const flashSuccess = computed(() => (page.props.flash as { success?: string })?.success);

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
