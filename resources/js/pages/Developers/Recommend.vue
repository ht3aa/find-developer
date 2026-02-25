<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import Footer from '@/components/Footer.vue';
import Navbar from '@/components/Navbar.vue';
import { home } from '@/routes';
import type { Developer } from '@/types/developer';

defineProps<{
    developer: Developer;
}>();
</script>

<template>
    <Head :title="`Recommend ${developer.name} | Find Developer`" />
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <main class="flex-1">
            <div class="mx-auto max-w-2xl px-4 py-12">
                <Link
                    :href="developer.profile_url ?? home()"
                    class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                >
                    <ArrowLeft class="size-4" />
                    Back to {{ developer.name }}'s profile
                </Link>

                <h1 class="text-2xl font-bold">
                    Recommend {{ developer.name }}
                </h1>
                <p class="mt-2 text-muted-foreground">
                    Recommendation form coming soon.
                </p>
            </div>
        </main>

        <Footer />
    </div>
</template>
