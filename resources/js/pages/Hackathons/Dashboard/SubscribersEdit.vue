<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Pencil } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as hackathonsIndex } from '@/routes/hackathons';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

type StatusOption = { value: string; label: string };

const props = defineProps<{
    hackathon: { id: number; title: string };
    subscriber: {
        id: number;
        developer: { id: number; name: string; slug: string; email: string | null } | null;
        message: string;
        status: string;
    };
    statusOptions: StatusOption[];
    updateUrl: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Hackathons', href: hackathonsIndex().url },
    { title: props.hackathon.title, href: '#' },
    { title: 'Subscribers', href: `/dashboard/hackathons/${props.hackathon.id}/subscribers` },
    { title: 'Edit', href: '#' },
];

const inputClass = 'flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm';
</script>

<template>
    <Head title="Edit Subscriber" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full space-y-6 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                    <Pencil class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <Heading
                        title="Edit subscriber"
                        :description="subscriber.developer ? `Update subscription for ${subscriber.developer.name}` : 'Update subscription'"
                    />
                </div>
            </div>

            <Card>
                <Form :action="updateUrl" method="post" v-slot="{ errors, processing, recentlySuccessful }">
                    <input type="hidden" name="_method" value="PUT" />
                    <CardHeader class="pb-4">
                        <h3 class="text-sm font-medium text-muted-foreground">
                            Subscriber details
                        </h3>
                    </CardHeader>
                    <CardContent class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div class="grid gap-2 lg:col-span-2">
                            <Label>Developer</Label>
                            <p class="text-sm font-medium">
                                <Link
                                    v-if="subscriber.developer"
                                    :href="`/developers/${subscriber.developer.slug}`"
                                    class="text-primary hover:underline"
                                >
                                    {{ subscriber.developer.name }}
                                </Link>
                                <span v-else class="text-muted-foreground">—</span>
                            </p>
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
                                    :selected="opt.value === subscriber.status"
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
                                :default-value="subscriber.message"
                                class="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >{{ subscriber.message }}</textarea>
                            <InputError :message="errors.message" />
                        </div>

                        <div class="flex flex-wrap items-center gap-3 pt-2 lg:col-span-2">
                            <Button :disabled="processing" type="submit">
                                Update subscriber
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
                                    Subscriber updated successfully
                                </span>
                            </Transition>
                        </div>
                    </CardContent>
                </Form>
            </Card>
        </div>
    </AppLayout>
</template>
