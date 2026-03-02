<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Users } from 'lucide-vue-next';
import Footer from '@/components/Footer.vue';
import Navbar from '@/components/Navbar.vue';
import SeoHead from '@/components/SeoHead.vue';
import { Card, CardContent, CardHeader } from '@/components/ui/card';

type PublicHackathonTeamsHackathon = {
    id: number;
    title: string;
    slug: string;
};

type PublicHackathonTeamMember = {
    id: number;
    position: string;
    position_label: string;
    developer: { id: number; name: string; slug: string; email: string | null } | null;
};

type PublicHackathonTeam = {
    id: number;
    title: string;
    logo_url: string | null;
    votes_count: number;
    has_voted: boolean;
    vote_url: string;
    members: PublicHackathonTeamMember[];
};

const props = defineProps<{
    hackathon: PublicHackathonTeamsHackathon;
    teams: PublicHackathonTeam[];
    canVote: boolean;
}>();

function toggleVote(team: PublicHackathonTeam): void {
    if (!props.canVote) return;
    router.post(
        team.vote_url,
        {},
        { preserveScroll: true },
    );
}
</script>

<template>
    <SeoHead
        :title="`${hackathon.title} – Teams`"
        :description="`Teams participating in ${hackathon.title}`"
        :canonical="`/hackathons/${hackathon.slug}/teams`"
    />
    <Head>
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-12 sm:px-6 lg:px-8">
            <header class="mb-8 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">
                        Hackathon teams
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

            <section>
                <div
                    v-if="teams.length === 0"
                    class="rounded-xl border border-dashed border-border py-16 text-center"
                >
                    <Users
                        class="mx-auto mb-4 h-12 w-12 text-muted-foreground"
                        aria-hidden="true"
                    />
                    <h2 class="text-lg font-semibold text-foreground">
                        No teams yet
                    </h2>
                    <p class="mt-2 text-sm text-muted-foreground">
                        Teams for this hackathon will appear here once they are created.
                    </p>
                </div>

                <div
                    v-else
                    class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <Card
                        v-for="team in teams"
                        :key="team.id"
                        class="flex h-full flex-col overflow-hidden"
                    >
                        <CardHeader class="flex flex-row items-center gap-4 pb-3">
                            <div
                                v-if="team.logo_url"
                                class="h-12 w-12 shrink-0 overflow-hidden rounded-xl bg-muted"
                            >
                                <img
                                    :src="team.logo_url"
                                    :alt="team.title"
                                    class="size-full object-cover"
                                />
                            </div>
                            <div class="min-w-0 flex-1">
                                <h2 class="truncate text-base font-semibold tracking-tight">
                                    {{ team.title }}
                                </h2>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    {{ team.members.length }}
                                    {{ team.members.length === 1 ? 'member' : 'members' }}
                                    ·
                                    {{ team.votes_count }}
                                    {{ team.votes_count === 1 ? 'vote' : 'votes' }}
                                </p>
                            </div>
                            <button
                                v-if="canVote"
                                type="button"
                                class="shrink-0 inline-flex items-center rounded-full border px-3 py-1 text-xs font-medium transition-colors"
                                :class="
                                    team.has_voted
                                        ? 'border-primary bg-primary text-primary-foreground hover:bg-primary/90'
                                        : 'border-border bg-background text-foreground hover:bg-muted'
                                "
                                @click="toggleVote(team)"
                            >
                                {{ team.has_voted ? 'Unvote' : 'Vote' }}
                            </button>
                        </CardHeader>
                        <CardContent class="flex-1 space-y-3 pt-0">
                            <div
                                v-if="team.members.length === 0"
                                class="rounded-md border border-dashed border-border px-3 py-2 text-xs text-muted-foreground"
                            >
                                No members yet.
                            </div>
                            <ul
                                v-else
                                class="space-y-2 text-sm"
                            >
                                <li
                                    v-for="member in team.members"
                                    :key="member.id"
                                    class="flex items-start justify-between gap-3 rounded-md bg-muted/40 px-3 py-2"
                                >
                                    <div class="min-w-0 flex-1">
                                        <template v-if="member.developer">
                                            <Link
                                                :href="`/developers/${member.developer.slug}`"
                                                class="block truncate font-medium text-primary hover:underline"
                                            >
                                                {{ member.developer.name }}
                                            </Link>
                                            <p
                                                v-if="member.developer.email"
                                                class="truncate text-xs text-muted-foreground"
                                            >
                                                {{ member.developer.email }}
                                            </p>
                                        </template>
                                        <span
                                            v-else
                                            class="text-xs text-muted-foreground"
                                        >
                                            Unknown developer
                                        </span>
                                    </div>
                                    <span class="shrink-0 rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary">
                                        {{ member.position_label }}
                                    </span>
                                </li>
                            </ul>
                        </CardContent>
                    </Card>
                </div>
            </section>
        </main>

        <Footer />
    </div>
</template>

