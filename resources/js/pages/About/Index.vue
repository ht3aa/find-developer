<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowRight, Mail, Target } from 'lucide-vue-next';
import { computed } from 'vue';
import Footer from '@/components/Footer.vue';
import Hero from '@/components/Hero.vue';
import Navbar from '@/components/Navbar.vue';
import SeoHead from '@/components/SeoHead.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription } from '@/components/ui/card';
import { home } from '@/routes';

const page = usePage();
const appName = computed(() => (page.props.name as string) || 'Find Developer');
const supportEmail = 'ht3aa2001@gmail.com';
const mailtoHref = `mailto:${supportEmail}`;
</script>

<template>
    <SeoHead
        title="About us"
        :description="`Learn about ${appName} — grow your developer presence and help companies find the right hire for their needs and team.`"
        canonical="/about"
    />
    <Head />
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <Hero
            badge="Our story"
            title="About us"
            :description="`How ${appName} helps developers build presence—and helps companies find the right developer for their needs and team.`"
            primary-action-label="Read more"
            primary-action-href="#about-main"
        />

        <div
            class="relative flex-1 overflow-hidden border-t border-border/60 bg-gradient-to-b from-muted/25 via-background to-background"
        >
            <div
                class="pointer-events-none absolute inset-0 -z-10"
                aria-hidden="true"
            >
                <div
                    class="absolute -top-32 left-1/2 size-[min(100vw,48rem)] -translate-x-1/2 rounded-full bg-primary/[0.06] blur-3xl dark:bg-primary/[0.09]"
                />
                <div
                    class="absolute top-[40%] -right-24 size-72 rounded-full bg-secondary/15 blur-3xl"
                />
                <div
                    class="absolute bottom-[10%] -left-16 size-64 rounded-full bg-primary/5 blur-3xl"
                />
            </div>

            <main
                id="about-main"
                tabindex="-1"
                class="relative z-0 mx-auto flex w-full max-w-3xl scroll-mt-24 flex-col gap-12 px-4 py-12 sm:gap-14 sm:px-6 sm:py-16 lg:px-8 lg:py-20"
            >
                <section aria-labelledby="about-mission-heading">
                    <div class="flex flex-col items-center gap-3 text-center">
                        <div
                            class="flex size-12 items-center justify-center rounded-2xl bg-primary/10 text-primary ring-1 ring-primary/15"
                        >
                            <Target
                                class="size-6"
                                aria-hidden="true"
                                stroke-width="2"
                            />
                        </div>
                        <h2
                            id="about-mission-heading"
                            class="text-2xl font-semibold tracking-tight text-foreground sm:text-3xl"
                        >
                            Our mission
                        </h2>
                    </div>
                    <Card
                        class="mt-6 border-primary/15 bg-gradient-to-br from-card via-card to-primary/[0.04] py-8 shadow-md ring-1 ring-border/80"
                    >
                        <CardContent class="px-6 sm:px-8">
                            <p
                                class="text-center text-base leading-relaxed text-muted-foreground sm:text-lg"
                            >
                                We want every developer to earn visibility for
                                who they are and what they have shipped, and we
                                want every company to find the best match for
                                their stack, scope, and culture—not the loudest
                                résumé. We also support communities that host
                                hackathons where developers gather to build
                                together—from signup and teams to demos and
                                results.
                            </p>
                        </CardContent>
                    </Card>
                </section>

                <section aria-labelledby="about-contact-heading">
                    <Card
                        class="overflow-hidden border-primary/15 bg-gradient-to-br from-card via-card to-primary/[0.05] py-0 shadow-md ring-1 ring-border/80 dark:to-primary/[0.08]"
                    >
                        <div
                            class="mx-auto flex max-w-lg flex-col items-center px-6 py-10 text-center sm:px-8 sm:py-12"
                        >
                            <div
                                class="flex size-14 items-center justify-center rounded-2xl bg-primary/10 text-primary ring-1 ring-primary/15"
                            >
                                <Mail
                                    class="size-7"
                                    aria-hidden="true"
                                    stroke-width="2"
                                />
                            </div>
                            <h2
                                id="about-contact-heading"
                                class="mt-5 text-2xl font-semibold tracking-tight sm:text-3xl"
                            >
                                Get in touch
                            </h2>
                            <CardDescription
                                class="mt-3 max-w-md text-pretty text-base leading-relaxed text-muted-foreground"
                            >
                                Questions, feedback, or ideas—we read every
                                message.
                            </CardDescription>
                            <p class="mt-4">
                                <a
                                    :href="mailtoHref"
                                    class="break-all text-sm font-medium text-primary underline-offset-4 transition-colors hover:underline focus-visible:rounded-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                >
                                    {{ supportEmail }}
                                </a>
                            </p>
                            <div
                                class="mt-8 flex w-full max-w-sm flex-col gap-3 sm:max-w-none sm:flex-row sm:justify-center"
                            >
                                <Button
                                    size="lg"
                                    class="w-full gap-2 rounded-xl sm:w-auto sm:min-w-[11rem]"
                                    as-child
                                >
                                    <a
                                        :href="mailtoHref"
                                        :aria-label="`Send email to ${supportEmail}`"
                                    >
                                        <Mail
                                            class="size-4 shrink-0 opacity-90"
                                            aria-hidden="true"
                                        />
                                        Email us
                                    </a>
                                </Button>
                                <Button
                                    variant="outline"
                                    size="lg"
                                    class="w-full rounded-xl sm:w-auto sm:min-w-[11rem]"
                                    as-child
                                >
                                    <Link
                                        :href="home()"
                                        class="inline-flex w-full items-center justify-center gap-2"
                                    >
                                        Browse developers
                                        <ArrowRight
                                            class="size-4 shrink-0"
                                            aria-hidden="true"
                                        />
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </Card>
                </section>
            </main>
        </div>

        <Footer />
    </div>
</template>
