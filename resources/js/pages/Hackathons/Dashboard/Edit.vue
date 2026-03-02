<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Trophy } from 'lucide-vue-next';
import HackathonController from '@/actions/App/Http/Controllers/HackathonController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as hackathonsIndex, edit as hackathonsEdit } from '@/routes/hackathons';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

export type DashboardHackathonEdit = {
    id: number;
    title: string;
    slug: string;
    body: string | null;
    image: string | null;
    image_url: string | null;
    youtube_url: string | null;
    reward_badge_id: number | null;
    reward_description: string | null;
    start_date: string | null;
    end_date: string | null;
};

type BadgeOption = { id: number; name: string };

type Props = {
    hackathon: DashboardHackathonEdit;
    badges: BadgeOption[];
};

const props = defineProps<Props>();

const inputClass = 'flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Hackathons', href: hackathonsIndex().url },
    { title: props.hackathon.title, href: hackathonsEdit(props.hackathon.id).url },
];
</script>

<template>
    <Head :title="`Edit ${hackathon.title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full space-y-6 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10"
                >
                    <Trophy class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <Heading
                        :title="`Edit ${hackathon.title}`"
                        description="Update the hackathon details"
                    />
                </div>
            </div>

            <Card class="lg:col-span-2">
                <Form
                    :action="HackathonController.update(hackathon.id)"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <input type="hidden" name="_method" value="PUT" />
                    <CardHeader class="pb-4">
                        <h3 class="text-sm font-medium text-muted-foreground">
                            Hackathon details
                        </h3>
                    </CardHeader>
                    <CardContent class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div class="grid gap-2 lg:col-span-2">
                            <Label for="title">Title <span class="text-destructive">*</span></Label>
                            <Input
                                id="title"
                                name="title"
                                :default-value="hackathon.title"
                                required
                                placeholder="e.g. Laravel Hackathon 2025"
                                class="transition-colors focus-visible:ring-2"
                            />
                            <InputError :message="errors.title" />
                        </div>

                        <div class="grid gap-2 lg:col-span-2">
                            <Label for="body">Body</Label>
                            <textarea
                                id="body"
                                name="body"
                                rows="6"
                                placeholder="Description, format with HTML if needed"
                                class="flex min-h-[120px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >{{ hackathon.body ?? '' }}</textarea>
                            <InputError :message="errors.body" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="image">Image path or URL</Label>
                            <Input
                                id="image"
                                name="image"
                                :default-value="hackathon.image ?? ''"
                                placeholder="Storage path or full URL"
                                class="transition-colors focus-visible:ring-2"
                            />
                            <p class="text-xs text-muted-foreground">
                                Path in storage or full image URL
                            </p>
                            <InputError :message="errors.image" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="youtube_url">YouTube URL</Label>
                            <Input
                                id="youtube_url"
                                name="youtube_url"
                                :default-value="hackathon.youtube_url ?? ''"
                                type="url"
                                placeholder="https://www.youtube.com/watch?v=..."
                                class="transition-colors focus-visible:ring-2"
                            />
                            <InputError :message="errors.youtube_url" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="reward_badge_id">Reward badge</Label>
                            <select
                                id="reward_badge_id"
                                name="reward_badge_id"
                                :class="inputClass"
                            >
                                <option value="">None</option>
                                <option
                                    v-for="b in badges"
                                    :key="b.id"
                                    :value="b.id"
                                    :selected="hackathon.reward_badge_id === b.id"
                                >
                                    {{ b.name }}
                                </option>
                            </select>
                            <InputError :message="errors.reward_badge_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="reward_description">Reward description</Label>
                            <textarea
                                id="reward_description"
                                name="reward_description"
                                rows="2"
                                placeholder="e.g. Winner receives..."
                                class="flex min-h-[60px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >{{ hackathon.reward_description ?? '' }}</textarea>
                            <InputError :message="errors.reward_description" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="start_date">Start date</Label>
                            <Input
                                id="start_date"
                                name="start_date"
                                :default-value="hackathon.start_date ?? ''"
                                type="date"
                                class="transition-colors focus-visible:ring-2"
                            />
                            <InputError :message="errors.start_date" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="end_date">End date</Label>
                            <Input
                                id="end_date"
                                name="end_date"
                                :default-value="hackathon.end_date ?? ''"
                                type="date"
                                class="transition-colors focus-visible:ring-2"
                            />
                            <InputError :message="errors.end_date" />
                        </div>

                        <div class="flex flex-wrap items-center gap-3 pt-2 lg:col-span-2">
                            <Button :disabled="processing" type="submit">
                                Update Hackathon
                            </Button>
                            <Button variant="outline" as-child>
                                <Link :href="hackathonsIndex().url">Cancel</Link>
                            </Button>
                            <Transition
                                enter-active-class="transition ease-out duration-200"
                                enter-from-class="opacity-0 translate-y-1"
                                leave-active-class="transition ease-in duration-150"
                                leave-to-class="opacity-0"
                            >
                                <span
                                    v-show="recentlySuccessful"
                                    class="inline-flex items-center gap-1.5 rounded-md bg-green-500/10 px-2.5 py-1 text-sm font-medium text-green-700 dark:text-green-400"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500" />
                                    Hackathon updated successfully
                                </span>
                            </Transition>
                        </div>
                    </CardContent>
                </Form>
            </Card>
        </div>
    </AppLayout>
</template>
