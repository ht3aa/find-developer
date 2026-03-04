<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Trophy } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import HackathonController from '@/actions/App/Http/Controllers/HackathonController';
import FileUpload from '@/components/FileUpload.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import SearchableSelect from '@/components/SearchableSelect.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import {
    index as hackathonsIndex,
    edit as hackathonsEdit,
} from '@/routes/hackathons';
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
    current_team_id_to_vote: number | null;
    enable_voting: boolean;
};

type BadgeOption = { id: number; name: string };

type TeamOption = { id: number; title: string };

type Props = {
    hackathon: DashboardHackathonEdit;
    badges: BadgeOption[];
    teams: TeamOption[];
};

const props = defineProps<Props>();

const page = usePage();
const formErrors = computed(
    () => (page.props.errors as Record<string, string>) ?? {},
);
const flashSuccess = computed(
    () => (page.props.flash as { success?: string })?.success,
);

const inputClass =
    'flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm';

const title = ref(props.hackathon.title);
const body = ref(props.hackathon.body ?? '');
const youtubeUrl = ref(props.hackathon.youtube_url ?? '');
const rewardBadgeId = ref<string | null>(
    props.hackathon.reward_badge_id != null
        ? String(props.hackathon.reward_badge_id)
        : null,
);
const rewardDescription = ref(props.hackathon.reward_description ?? '');
const startDate = ref(props.hackathon.start_date ?? '');
const endDate = ref(props.hackathon.end_date ?? '');
const currentTeamIdToVote = ref<string | null>(
    props.hackathon.current_team_id_to_vote != null
        ? String(props.hackathon.current_team_id_to_vote)
        : null,
);
const enableVoting = ref(props.hackathon.enable_voting);
const currentTeamSelectOpen = ref(false);
const imageFile = ref<File | null>(null);
const imageUploadRef = ref<InstanceType<typeof FileUpload> | null>(null);
const submitting = ref(false);

watch(
    () => props.hackathon,
    (h) => {
        title.value = h.title;
        body.value = h.body ?? '';
        youtubeUrl.value = h.youtube_url ?? '';
        rewardBadgeId.value =
            h.reward_badge_id != null ? String(h.reward_badge_id) : null;
        rewardDescription.value = h.reward_description ?? '';
        startDate.value = h.start_date ?? '';
        endDate.value = h.end_date ?? '';
        currentTeamIdToVote.value =
            h.current_team_id_to_vote != null
                ? String(h.current_team_id_to_vote)
                : null;
        enableVoting.value = h.enable_voting;
    },
    { immediate: true },
);

function onCurrentTeamOpenChange(open: boolean): void {
    currentTeamSelectOpen.value = open;
}

function buildPayload(): Record<string, unknown> {
    const payload: Record<string, unknown> = {
        title: title.value,
        body: body.value || null,
        youtube_url: youtubeUrl.value || null,
        reward_badge_id: rewardBadgeId.value || null,
        reward_description: rewardDescription.value || null,
        start_date: startDate.value || null,
        end_date: endDate.value || null,
        current_team_id_to_vote: currentTeamIdToVote.value || null,
        enable_voting: enableVoting.value,
    };
    if (imageFile.value) {
        payload.image = imageFile.value;
    }
    return payload;
}

function submitForm(): void {
    submitting.value = true;
    router.put(
        HackathonController.update.url(props.hackathon.id),
        buildPayload(),
        {
            preserveScroll: true,
            forceFormData: !!imageFile.value,
            onSuccess: () => {
                imageFile.value = null;
                imageUploadRef.value?.clear();
            },
            onFinish: () => {
                submitting.value = false;
            },
        },
    );
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Hackathons', href: hackathonsIndex().url },
    {
        title: props.hackathon.title,
        href: hackathonsEdit(props.hackathon.id).url,
    },
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
                <form @submit.prevent="submitForm">
                    <CardHeader class="pb-4">
                        <h3 class="text-sm font-medium text-muted-foreground">
                            Hackathon details
                        </h3>
                    </CardHeader>
                    <CardContent class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div class="grid gap-2 lg:col-span-2">
                            <Label for="title"
                                >Title
                                <span class="text-destructive">*</span></Label
                            >
                            <Input
                                id="title"
                                v-model="title"
                                name="title"
                                required
                                placeholder="e.g. Laravel Hackathon 2025"
                                class="transition-colors focus-visible:ring-2"
                            />
                            <InputError :message="formErrors.title" />
                        </div>

                        <div class="grid gap-2 lg:col-span-2">
                            <Label for="body">Body</Label>
                            <textarea
                                id="body"
                                v-model="body"
                                name="body"
                                rows="6"
                                placeholder="Description, format with HTML if needed"
                                class="flex min-h-[120px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                            />
                            <InputError :message="formErrors.body" />
                        </div>

                        <FileUpload
                            id="image"
                            ref="imageUploadRef"
                            v-model="imageFile"
                            label="Image (JPEG, PNG, GIF, WebP, max 2MB)"
                            accept=".jpeg,.jpg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp"
                            :existing-url="hackathon.image_url ?? null"
                            existing-label="View current image"
                            :error="formErrors.image"
                        />

                        <div class="grid gap-2">
                            <Label for="youtube_url">YouTube URL</Label>
                            <Input
                                id="youtube_url"
                                v-model="youtubeUrl"
                                name="youtube_url"
                                type="url"
                                placeholder="https://www.youtube.com/watch?v=..."
                                class="transition-colors focus-visible:ring-2"
                            />
                            <InputError :message="formErrors.youtube_url" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="reward_badge_id">Reward badge</Label>
                            <select
                                id="reward_badge_id"
                                v-model="rewardBadgeId"
                                name="reward_badge_id"
                                :class="inputClass"
                            >
                                <option value="">None</option>
                                <option
                                    v-for="b in badges"
                                    :key="b.id"
                                    :value="b.id"
                                >
                                    {{ b.name }}
                                </option>
                            </select>
                            <InputError :message="formErrors.reward_badge_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="reward_description"
                                >Reward description</Label
                            >
                            <textarea
                                id="reward_description"
                                v-model="rewardDescription"
                                name="reward_description"
                                rows="2"
                                placeholder="e.g. Winner receives..."
                                class="flex min-h-[60px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                            />
                            <InputError
                                :message="formErrors.reward_description"
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="current_team_id_to_vote"
                                >Current team in voting</Label
                            >
                            <SearchableSelect
                                id="current_team_id_to_vote"
                                v-model="currentTeamIdToVote"
                                :options="[
                                    {
                                        value: null,
                                        label: 'No active voting team',
                                    },
                                    ...teams.map((t) => ({
                                        value: String(t.id),
                                        label: t.title,
                                    })),
                                ]"
                                :open="currentTeamSelectOpen"
                                placeholder="Select team in voting..."
                                @update:open="onCurrentTeamOpenChange"
                            />
                            <InputError
                                :message="formErrors.current_team_id_to_vote"
                            />
                        </div>

                        <div class="grid gap-2">
                            <div class="flex items-center gap-2">
                                <Checkbox
                                    id="enable_voting"
                                    v-model:checked="enableVoting"
                                    name="enable_voting"
                                    value="1"
                                    class="h-5 w-5"
                                />
                                <Label
                                    for="enable_voting"
                                    class="cursor-pointer font-normal"
                                >
                                    Enable voting for subscribers
                                </Label>
                            </div>
                            <p class="text-xs text-muted-foreground">
                                When enabled, subscribed developers can vote for
                                teams on the public teams page.
                            </p>
                            <InputError :message="formErrors.enable_voting" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="start_date">Start date</Label>
                            <Input
                                id="start_date"
                                v-model="startDate"
                                name="start_date"
                                type="date"
                                class="transition-colors focus-visible:ring-2"
                            />
                            <InputError :message="formErrors.start_date" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="end_date">End date</Label>
                            <Input
                                id="end_date"
                                v-model="endDate"
                                name="end_date"
                                type="date"
                                class="transition-colors focus-visible:ring-2"
                            />
                            <InputError :message="formErrors.end_date" />
                        </div>

                        <div
                            class="flex flex-wrap items-center gap-3 pt-2 lg:col-span-2"
                        >
                            <Button :disabled="submitting" type="submit">
                                {{
                                    submitting
                                        ? 'Updating...'
                                        : 'Update Hackathon'
                                }}
                            </Button>
                            <Button variant="outline" as-child>
                                <Link :href="hackathonsIndex().url"
                                    >Cancel</Link
                                >
                            </Button>
                            <Transition
                                enter-active-class="transition ease-out duration-200"
                                enter-from-class="opacity-0 translate-y-1"
                                leave-active-class="transition ease-in duration-150"
                                leave-to-class="opacity-0"
                            >
                                <span
                                    v-show="flashSuccess"
                                    class="inline-flex items-center gap-1.5 rounded-md bg-green-500/10 px-2.5 py-1 text-sm font-medium text-green-700 dark:text-green-400"
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-green-500"
                                    />
                                    Hackathon updated successfully
                                </span>
                            </Transition>
                        </div>
                    </CardContent>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
