<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Footer from '@/components/Footer.vue';
import Hero from '@/components/Hero.vue';
import Navbar from '@/components/Navbar.vue';
import SeoHead from '@/components/SeoHead.vue';

const page = usePage();
const appName = computed(() => (page.props.name as string) || 'Find Developer');
</script>

<template>
    <SeoHead
        title="About us"
        :description="`Learn about ${appName} — developer portfolios, hackathons, and community in one place.`"
        canonical="/about"
    />
    <Head />
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <Hero
            badge="Company"
            title="About us"
            :description="`What ${appName} is, who it’s for, and why we built it.`"
        />

        <article
            class="mx-auto w-full max-w-3xl px-4 pb-16 sm:px-6 lg:px-8"
        >
            <div
                class="prose prose-neutral dark:prose-invert max-w-none prose-headings:scroll-mt-24 prose-a:text-primary"
            >
                <p>
                    {{ appName }} is a platform for showcasing developer work and
                    running hackathon-style events in one place. We help
                    developers present skills, experience, projects, and
                    writing, while teams and organizers coordinate events, teams,
                    voting, badges, and updates without juggling a dozen tools.
                </p>

                <h2>Our mission</h2>
                <p>
                    We want it to be easier for developers to be discovered for
                    who they are and what they have built, and easier for
                    communities to run engaging, transparent hackathons—from
                    signup to teams to results.
                </p>

                <h2>What you can do here</h2>
                <ul>
                    <li>
                        <strong>Portfolios:</strong> Rich developer profiles with
                        skills, experience, projects, and blog posts.
                    </li>
                    <li>
                        <strong>Hackathons:</strong> Public event pages, teams,
                        voting, and subscriber updates when organizers enable them.
                    </li>
                    <li>
                        <strong>Recognition:</strong> Badges and achievements to
                        highlight milestones and participation.
                    </li>
                    <li>
                        <strong>Newsletter:</strong> Optional updates so people
                        can stay in the loop when they choose to subscribe.
                    </li>
                </ul>

                <h2>Who we serve</h2>
                <p>
                    Individual developers building a public presence, organizers
                    and communities running hackathons, and teams (including HR
                    and admins on the platform) who need structured access and
                    workflows. Roles and permissions keep each experience
                    appropriate to the account.
                </p>

                <h2>Contact</h2>
                <p>
                    Questions or ideas? Reach us at
                    <a href="mailto:ht3aa2001@gmail.com">ht3aa2001@gmail.com</a>.
                </p>
            </div>
        </article>

        <Footer />
    </div>
</template>
