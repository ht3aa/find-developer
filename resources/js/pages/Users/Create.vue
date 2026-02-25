<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { UserCog } from 'lucide-vue-next';
import UserController from '@/actions/App/Http/Controllers/Dashboard/UserController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as usersIndex, create as usersCreate } from '@/routes/users';
import { dashboard } from '@/routes';
import { userTypeOptions } from '@/types/user';
import type { BreadcrumbItem } from '@/types';

type RoleOption = {
    id: number;
    name: string;
    guard_name: string;
};

type Props = {
    roles: RoleOption[];
};

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Users', href: usersIndex().url },
    { title: 'Create', href: usersCreate().url },
];
</script>

<template>
    <Head title="Create User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl space-y-6 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10"
                >
                    <UserCog class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <Heading
                        title="Create User"
                        description="Add a new user and assign roles"
                    />
                </div>
            </div>

            <Card>
                <Form
                    :action="UserController.store()"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <CardHeader class="pb-4">
                        <h3 class="text-sm font-medium text-muted-foreground">
                            User details
                        </h3>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="name">Name <span class="text-destructive">*</span></Label>
                                <Input
                                    id="name"
                                    name="name"
                                    required
                                    placeholder="Full name"
                                    class="transition-colors focus-visible:ring-2"
                                />
                                <InputError :message="errors.name" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="email">Email <span class="text-destructive">*</span></Label>
                                <Input
                                    id="email"
                                    name="email"
                                    type="email"
                                    required
                                    placeholder="email@example.com"
                                    class="transition-colors focus-visible:ring-2"
                                />
                                <InputError :message="errors.email" />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="password">Password <span class="text-destructive">*</span></Label>
                                <Input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    placeholder="••••••••"
                                    class="transition-colors focus-visible:ring-2"
                                />
                                <InputError :message="errors.password" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="password_confirmation">Confirm password <span class="text-destructive">*</span></Label>
                                <Input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    required
                                    placeholder="••••••••"
                                    class="transition-colors focus-visible:ring-2"
                                />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="user_type">User type <span class="text-destructive">*</span></Label>
                                <select
                                    id="user_type"
                                    name="user_type"
                                    required
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                >
                                    <option value="">Select type</option>
                                    <option
                                        v-for="opt in userTypeOptions"
                                        :key="opt.value"
                                        :value="opt.value"
                                    >
                                        {{ opt.label }}
                                    </option>
                                </select>
                                <InputError :message="errors.user_type" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="linkedin_url">LinkedIn URL</Label>
                                <Input
                                    id="linkedin_url"
                                    name="linkedin_url"
                                    type="url"
                                    placeholder="https://linkedin.com/in/..."
                                    class="transition-colors focus-visible:ring-2"
                                />
                                <InputError :message="errors.linkedin_url" />
                            </div>
                        </div>

                        <div class="flex items-center justify-between rounded-lg border p-4">
                            <div class="space-y-0.5">
                                <Label for="can_access_admin_panel" class="text-base">
                                    Can access admin panel
                                </Label>
                                <p class="text-sm text-muted-foreground">
                                    Allow this user to access the dashboard
                                </p>
                            </div>
                            <input type="hidden" name="can_access_admin_panel" value="0" />
                            <Checkbox
                                id="can_access_admin_panel"
                                name="can_access_admin_panel"
                                value="1"
                                class="h-5 w-5"
                            />
                        </div>
                        <InputError :message="errors.can_access_admin_panel" />

                        <Separator />

                        <div class="space-y-3">
                            <h3 class="text-sm font-medium text-muted-foreground">
                                Roles
                            </h3>
                            <div class="flex flex-wrap gap-4 rounded-lg border p-4">
                                <div
                                    v-for="role in roles"
                                    :key="role.id"
                                    class="flex items-center space-x-2"
                                >
                                    <Checkbox
                                        :id="`role-${role.id}`"
                                        name="role_ids[]"
                                        :value="role.id"
                                        class="h-4 w-4"
                                    />
                                    <Label
                                        :for="`role-${role.id}`"
                                        class="text-sm font-normal"
                                    >
                                        {{ role.name }}
                                        <span class="text-muted-foreground">({{ role.guard_name }})</span>
                                    </Label>
                                </div>
                                <p
                                    v-if="!roles.length"
                                    class="text-sm text-muted-foreground"
                                >
                                    No roles defined. Create roles first.
                                </p>
                            </div>
                            <InputError :message="errors.role_ids" />
                        </div>

                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <Button :disabled="processing" type="submit">
                                Create User
                            </Button>
                            <Button variant="outline" as-child>
                                <Link :href="usersIndex().url">Cancel</Link>
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
                                    User created successfully
                                </span>
                            </Transition>
                        </div>
                    </CardContent>
                </Form>
            </Card>
        </div>
    </AppLayout>
</template>
