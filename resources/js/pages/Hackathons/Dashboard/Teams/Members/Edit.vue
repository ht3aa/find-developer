<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Pencil } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import SearchableSelect from '@/components/SearchableSelect.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as hackathonsIndex } from '@/routes/hackathons';
import type { BreadcrumbItem } from '@/types';

type DeveloperOption = { id: number; name: string };
type PositionOption = { value: string; label: string };

const props = defineProps<{
    hackathon: { id: number; title: string };
    team: { id: number; title: string };
    member: {
        id: number;
        developer_id: number;
        developer: { id: number; name: string; slug: string; email: string | null } | null;
        position: string;
    };
    developers: DeveloperOption[];
    positionOptions: PositionOption[];
    updateUrl: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Hackathons', href: hackathonsIndex().url },
    { title: props.hackathon.title, href: '#' },
    { title: 'Teams', href: `/dashboard/hackathons/${props.hackathon.id}/teams` },
    { title: props.team.title, href: `/dashboard/hackathons/${props.hackathon.id}/teams/${props.team.id}/members` },
    { title: 'Edit member', href: '#' },
];
const developerModel = ref<string | null>(String(props.member.developer_id));
const positionModel = ref<string | null>(props.member.position);
const developerSelectOpen = ref(false);
const positionSelectOpen = ref(false);

watch(
    () => props.member,
    (m) => {
        developerModel.value = String(m.developer_id);
        positionModel.value = m.position;
    },
    { immediate: true },
);

function onDeveloperOpenChange(open: boolean): void {
    developerSelectOpen.value = open;
}

function onPositionOpenChange(open: boolean): void {
    positionSelectOpen.value = open;
}
</script>

<template>
    <Head title="Edit Team Member" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full space-y-6 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                    <Pencil class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <Heading
                        title="Edit team member"
                        :description="member.developer ? `Update ${member.developer.name} in ${team.title}` : 'Update member'"
                    />
                </div>
            </div>

            <Card>
                <Form :action="updateUrl" method="post" v-slot="{ errors, processing, recentlySuccessful }">
                    <input type="hidden" name="_method" value="PUT" />
                    <CardHeader class="pb-4">
                        <h3 class="text-sm font-medium text-muted-foreground">
                            Member details
                        </h3>
                    </CardHeader>
                    <CardContent class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="developer_id">Developer <span class="text-destructive">*</span></Label>
                            <SearchableSelect
                                id="developer_id"
                                v-model="developerModel"
                                :options="developers.map((d) => ({ value: String(d.id), label: d.name }))"
                                :open="developerSelectOpen"
                                options-url="/api/developers"
                                placeholder="Select a developer..."
                                @update:open="onDeveloperOpenChange"
                            />
                            <input type="hidden" name="developer_id" :value="developerModel ?? ''" />
                            <InputError :message="errors.developer_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="position">Position <span class="text-destructive">*</span></Label>
                            <SearchableSelect
                                id="position"
                                v-model="positionModel"
                                :options="positionOptions.map((opt) => ({ value: opt.value, label: opt.label }))"
                                placeholder="Select a position..."
                                :open="positionSelectOpen"
                                @update:open="onPositionOpenChange"
                            />
                            <input type="hidden" name="position" :value="positionModel ?? ''" />
                            <InputError :message="errors.position" />
                        </div>

                        <div class="flex flex-wrap items-center gap-3 pt-2 lg:col-span-2">
                            <Button :disabled="processing" type="submit">
                                Update member
                            </Button>
                            <Button variant="outline" as-child>
                                <Link :href="`/dashboard/hackathons/${hackathon.id}/teams/${team.id}/members`">
                                    Cancel
                                </Link>
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
                                    Member updated successfully
                                </span>
                            </Transition>
                        </div>
                    </CardContent>
                </Form>
            </Card>
        </div>
    </AppLayout>
</template>
