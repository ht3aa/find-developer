<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { UserPlus } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as hackathonsIndex } from '@/routes/hackathons';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

type DeveloperOption = { id: number; name: string };
type StatusOption = { value: string; label: string };

const props = defineProps<{
    hackathon: { id: number; title: string };
    developers: DeveloperOption[];
    statusOptions: StatusOption[];
    storeUrl: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Hackathons', href: hackathonsIndex().url },
    { title: props.hackathon.title, href: '#' },
    { title: 'Subscribers', href: `/dashboard/hackathons/${props.hackathon.id}/subscribers` },
    { title: 'Add', href: '#' },
];

const inputClass = 'flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm';
</script>

<template>
    <Head title="Add Subscriber" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full space-y-6 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                    <UserPlus class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <Heading
                        title="Add subscriber"
                        :description="`Add a developer as subscriber to ${hackathon.title}`"
                    />
                </div>
            </div>

            <Card>
                <Form :action="storeUrl" method="post" v-slot="{ errors, processing, recentlySuccessful }">
                    <CardHeader class="pb-4">
                        <h3 class="text-sm font-medium text-muted-foreground">
                            Subscriber details
                        </h3>
                    </CardHeader>
                    <CardContent class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="developer_id">Developer <span class="text-destructive">*</span></Label>
                            <select
                                id="developer_id"
                                name="developer_id"
                                required
                                :class="inputClass"
                            >
                                <option value="">Select a developer</option>
                                <option
                                    v-for="d in developers"
                                    :key="d.id"
                                    :value="d.id"
                                >
                                    {{ d.name }}
                                </option>
                            </select>
                            <p v-if="developers.length === 0" class="text-xs text-muted-foreground">
                                All developers are already subscribed.
                            </p>
                            <InputError :message="errors.developer_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="status">Status <span class="text-destructive">*</span></Label>
                            <select
                                id="status"
                                name="status"
                                required
                                :class="inputClass"
                            >
                                <option
                                    v-for="opt in statusOptions"
                                    :key="opt.value"
                                    :value="opt.value"
                                >
                                    {{ opt.label }}
                                </option>
                            </select>
                            <InputError :message="errors.status" />
                        </div>

                        <div class="grid gap-2 lg:col-span-2">
                            <Label for="message">Message <span class="text-destructive">*</span></Label>
                            <textarea
                                id="message"
                                name="message"
                                required
                                rows="4"
                                placeholder="Confirmation or note from the developer"
                                class="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            />
                            <InputError :message="errors.message" />
                        </div>

                        <div class="flex flex-wrap items-center gap-3 pt-2 lg:col-span-2">
                            <Button :disabled="processing || developers.length === 0" type="submit">
                                Add subscriber
                            </Button>
                            <Button variant="outline" as-child>
                                <Link :href="`/dashboard/hackathons/${hackathon.id}/subscribers`">
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
                                    Subscriber added successfully
                                </span>
                            </Transition>
                        </div>
                    </CardContent>
                </Form>
            </Card>
        </div>
    </AppLayout>
</template>
