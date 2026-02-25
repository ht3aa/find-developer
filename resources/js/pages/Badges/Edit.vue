<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Award } from 'lucide-vue-next';
import BadgeController from '@/actions/App/Http/Controllers/BadgeController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as badgesIndex, edit as badgesEdit } from '@/routes/badges';
import { dashboard } from '@/routes';
import type { Badge } from '@/types/badge';
import type { BreadcrumbItem } from '@/types';

type Props = {
    badge: Badge;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Badges', href: badgesIndex().url },
    { title: props.badge.name, href: badgesEdit(props.badge.id).url },
];
</script>

<template>
    <Head :title="`Edit ${badge.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl space-y-6 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10"
                >
                    <Award class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <Heading
                        :title="`Edit ${badge.name}`"
                        description="Update the badge details"
                    />
                </div>
            </div>

            <Card>
                <Form
                    :action="BadgeController.update(badge.id)"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <input type="hidden" name="_method" value="PUT" />
                    <CardHeader class="pb-4">
                        <h3 class="text-sm font-medium text-muted-foreground">
                            Badge details
                        </h3>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="space-y-4">
                            <div class="grid gap-2">
                                <Label for="name">Name</Label>
                                <Input
                                    id="name"
                                    name="name"
                                    :default-value="badge.name"
                                    required
                                    placeholder="e.g. Laravel Expert"
                                    class="transition-colors focus-visible:ring-2"
                                />
                                <InputError :message="errors.name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="description">Description</Label>
                                <Input
                                    id="description"
                                    name="description"
                                    :default-value="badge.description ?? ''"
                                    placeholder="Brief description of what this badge represents"
                                    class="transition-colors focus-visible:ring-2"
                                />
                                <InputError :message="errors.description" />
                            </div>
                        </div>

                        <Separator />

                        <div class="space-y-4">
                            <h3 class="text-sm font-medium text-muted-foreground">
                                Appearance
                            </h3>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="grid gap-2">
                                    <Label for="icon">Icon</Label>
                                    <Input
                                        id="icon"
                                        name="icon"
                                        :default-value="badge.icon ?? ''"
                                        placeholder="e.g. heroicon-o-star"
                                        class="transition-colors focus-visible:ring-2"
                                    />
                                    <p class="text-xs text-muted-foreground">
                                        Heroicon or custom icon identifier
                                    </p>
                                    <InputError :message="errors.icon" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="color">Color</Label>
                                    <Input
                                        id="color"
                                        name="color"
                                        :default-value="badge.color ?? ''"
                                        placeholder="e.g. #3B82F6"
                                        class="font-mono text-sm transition-colors focus-visible:ring-2"
                                    />
                                    <p class="text-xs text-muted-foreground">
                                        Hex color for badge display
                                    </p>
                                    <InputError :message="errors.color" />
                                </div>
                            </div>
                        </div>

                        <Separator />

                        <div class="space-y-2">
                            <div class="flex items-center justify-between rounded-lg border p-4">
                                <div class="space-y-0.5">
                                    <Label for="is_active" class="text-base">
                                        Active
                                    </Label>
                                    <p class="text-sm text-muted-foreground">
                                        Inactive badges won't appear for
                                        assignment
                                    </p>
                                </div>
                                <input type="hidden" name="is_active" value="0" />
                                <Checkbox
                                    id="is_active"
                                    name="is_active"
                                    value="1"
                                    :default-value="badge.is_active"
                                    class="h-5 w-5"
                                />
                            </div>
                            <InputError :message="errors.is_active" />
                        </div>

                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <Button :disabled="processing" type="submit">
                                Update Badge
                            </Button>
                            <Button variant="outline" as-child>
                                <Link :href="badgesIndex().url">Cancel</Link>
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
                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-green-500"
                                    />
                                    Badge updated successfully
                                </span>
                            </Transition>
                        </div>
                    </CardContent>
                </Form>
            </Card>
        </div>
    </AppLayout>
</template>
