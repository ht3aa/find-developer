<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Users } from 'lucide-vue-next';
import DeveloperCardSection from '@/components/DeveloperCardSection.vue';
import Footer from '@/components/Footer.vue';
import Navbar from '@/components/Navbar.vue';
import SeoHead from '@/components/SeoHead.vue';

const props = defineProps<{
    hackathon: {
        id: number;
        title: string;
        slug: string;
    };
    developerIds: number[];
}>();
</script>

<template>
    <SeoHead
        :title="`${hackathon.title} – Subscribers`"
        :description="`Developers registered for ${hackathon.title}`"
        :canonical="`/hackathons/${hackathon.slug}/subscribers`"
    />
    <Head>
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <main
            class="mx-auto w-full max-w-7xl flex-1 px-4 py-12 sm:px-6 lg:px-8"
        >
            <header
                class="mb-8 flex flex-wrap items-center justify-between gap-4"
            >
                <div>
                    <p class="text-sm font-medium text-muted-foreground">
                        Registered developers
                    </p>
                    <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">
                        {{ hackathon.title }}
                    </h1>
                </div>
                <Link
                    :href="`/hackathons/${hackathon.slug}`"
                    class="text-sm font-medium text-primary hover:underline"
                >
                    ← Back to hackathon
                </Link>
            </header>

            <div
                v-if="developerIds.length === 0"
                class="flex flex-col items-center gap-4 rounded-lg border border-dashed border-border py-12 text-center text-muted-foreground"
            >
                <Users class="size-12 shrink-0 opacity-50" aria-hidden="true" />
                <p>No developers have registered for this hackathon yet.</p>
                <Link
                    :href="`/hackathons/${hackathon.slug}`"
                    class="text-sm font-medium text-primary hover:underline"
                >
                    View hackathon details
                </Link>
            </div>
            <DeveloperCardSection
                v-else
                :developer-ids="developerIds"
            />
        </main>

        <Footer />
    </div>
</template>
