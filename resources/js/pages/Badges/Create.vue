<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import BadgeController from '@/actions/App/Http/Controllers/BadgeController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as badgesIndex, create as badgesCreate } from '@/routes/badges';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Badges', href: badgesIndex().url },
    { title: 'Create', href: badgesCreate().url },
];
</script>

<template>
    <Head title="Create Badge" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl space-y-6 rounded-xl p-4">
            <Heading
                title="Create Badge"
                description="Add a new badge that can be assigned to developers"
            />

            <Form
                v-bind="BadgeController.store.form()"
                class="space-y-6"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        name="name"
                        required
                        placeholder="e.g. Laravel Expert"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <Input
                        id="description"
                        name="description"
                        placeholder="Optional description"
                    />
                    <InputError :message="errors.description" />
                </div>

                <div class="grid gap-2">
                    <Label for="icon">Icon</Label>
                    <Input
                        id="icon"
                        name="icon"
                        placeholder="e.g. heroicon-o-star"
                    />
                    <InputError :message="errors.icon" />
                </div>

                <div class="grid gap-2">
                    <Label for="color">Color</Label>
                    <Input
                        id="color"
                        name="color"
                        placeholder="e.g. #3B82F6"
                    />
                    <InputError :message="errors.color" />
                </div>

                <div class="flex items-center space-x-2">
                    <input type="hidden" name="is_active" value="0" />
                    <Checkbox id="is_active" name="is_active" value="1" />
                    <Label for="is_active" class="font-normal">Active</Label>
                </div>
                <InputError :message="errors.is_active" />

                <div class="flex items-center gap-4">
                    <Button :disabled="processing" type="submit">
                        Create Badge
                    </Button>
                    <Button variant="outline" as-child>
                        <Link :href="badgesIndex().url">Cancel</Link>
                    </Button>
                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p
                            v-show="recentlySuccessful"
                            class="text-sm text-muted-foreground"
                        >
                            Badge created successfully.
                        </p>
                    </Transition>
                </div>
            </Form>
        </div>
    </AppLayout>
</template>
