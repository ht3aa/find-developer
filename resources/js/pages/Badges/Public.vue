<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Award } from 'lucide-vue-next';
import BadgeIcon from '@/components/BadgeIcon.vue';
import Footer from '@/components/Footer.vue';
import Hero from '@/components/Hero.vue';
import Navbar from '@/components/Navbar.vue';
import SeoHead from '@/components/SeoHead.vue';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { home } from '@/routes';
import type { Badge as BadgeType } from '@/types/badge';

defineProps<{
    badges: BadgeType[];
}>();
</script>

<template>
    <SeoHead
        title="Badges"
        description="Badges represent skills, achievements, and certifications earned by our developers. Browse the full catalog."
        canonical="/badges"
    />
    <Head>
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <Hero
            badge="Badge catalog"
            title="Explore all badges"
            description="Badges represent skills, achievements, and certifications earned by our developers. Browse the full catalog below."
        />

        <section class="mx-auto w-full max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div
                v-if="badges.length === 0"
                class="rounded-xl border border-dashed border-border py-16 text-center"
            >
                <Award
                    class="mx-auto mb-4 h-12 w-12 text-muted-foreground"
                    aria-hidden="true"
                />
                <h2 class="text-lg font-semibold text-foreground">
                    No badges yet
                </h2>
                <p class="mt-2 text-sm text-muted-foreground">
                    Badges will appear here once they are created and activated.
                </p>
            </div>
            <div
                v-else
                class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <Card
                    v-for="badge in badges"
                    :key="badge.id"
                    class="overflow-hidden transition-shadow hover:shadow-md"
                >
                    <CardHeader class="pb-2">
                        <div
                            class="flex items-center gap-3"
                            :style="{
                                '--badge-color': badge.color || 'hsl(var(--primary))',
                            }"
                        >
                            <div
                                v-if="badge.icon != null"
                                class="flex size-12 shrink-0 items-center justify-center rounded-xl"
                                :class="
                                    badge.color
                                        ? ''
                                        : 'bg-primary/10 text-primary'
                                "
                                :style="
                                    badge.color
                                        ? {
                                              backgroundColor: `${badge.color}20`,
                                              color: badge.color,
                                          }
                                        : undefined
                                "
                            >
                                <BadgeIcon
                                    :icon="badge.icon"
                                    icon-class="size-6 shrink-0"
                                />
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3
                                    class="truncate font-semibold tracking-tight"
                                >
                                    {{ badge.name }}
                                </h3>
                                <p
                                    v-if="badge.developers_count != null"
                                    class="text-xs text-muted-foreground"
                                >
                                    {{ badge.developers_count }}
                                    {{ badge.developers_count === 1 ? 'developer' : 'developers' }}
                                </p>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent v-if="badge.description != null" class="pt-0">
                        <p class="line-clamp-3 text-sm text-muted-foreground">
                            {{ badge.description }}
                        </p>
                    </CardContent>
                </Card>
            </div>
        </section>

        <Footer />
    </div>
</template>
