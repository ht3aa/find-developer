<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DeveloperCardSection from '@/components/DeveloperCardSection.vue';
import Footer from '@/components/Footer.vue';
import Hero from '@/components/Hero.vue';
import Navbar from '@/components/Navbar.vue';
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
            secondary-action-label="Sign up"
        />

        <DeveloperCardSection />

        <Footer />
    </div>
</template>
