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
import { index as usersIndex, edit as usersEdit } from '@/routes/users';
import { dashboard } from '@/routes';
import { userTypeOptions } from '@/types/user';
import type { BreadcrumbItem } from '@/types';

type RoleOption = {
    id: number;
    name: string;
    guard_name: string;
};

type UserFormData = {
    id: number;
    name: string;
    email: string;
    user_type: string;
    can_access_admin_panel: boolean;
    linkedin_url: string | null;
    role_ids: number[];
};

type Props = {
    user: UserFormData;
    roles: RoleOption[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Users', href: usersIndex().url },
    { title: props.user.name, href: usersEdit(props.user.id).url },
];

function hasRole(user: UserFormData, roleId: number): boolean {
    return (user.role_ids ?? []).includes(roleId);
}
</script>

<template>
    <Head :title="`Edit ${user.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full space-y-6 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10"
                >
                    <UserCog class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <Heading
                        :title="`Edit ${user.name}`"
                        description="Update user details and roles"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <Card class="lg:col-span-2">
                    <Form
                        :action="UserController.update(user.id)"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <input type="hidden" name="_method" value="PUT" />
                    <CardHeader class="pb-4">
                        <h3 class="text-sm font-medium text-muted-foreground">
                            User details
                        </h3>
                    </CardHeader>
                    <CardContent class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="name">Name <span class="text-destructive">*</span></Label>
                                <Input
                                    id="name"
                                    name="name"
                                    :default-value="user.name"
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
                                    :default-value="user.email"
                                    required
                                    placeholder="email@example.com"
                                    class="transition-colors focus-visible:ring-2"
                                />
                                <InputError :message="errors.email" />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="password">New password</Label>
                                <Input
                                    id="password"
                                    name="password"
                                    type="password"
                                    placeholder="Leave blank to keep current"
                                    class="transition-colors focus-visible:ring-2"
                                />
                                <p class="text-xs text-muted-foreground">
                                    Leave blank to keep current password
                                </p>
                                <InputError :message="errors.password" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="password_confirmation">Confirm new password</Label>
                                <Input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
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
                                    <option
                                        v-for="opt in userTypeOptions"
                                        :key="opt.value"
                                        :value="opt.value"
                                        :selected="opt.value === user.user_type"
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
                                    :default-value="user.linkedin_url ?? ''"
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
                                :default-checked="user.can_access_admin_panel"
                                class="h-5 w-5"
                            />
                        </div>
                        <InputError :message="errors.can_access_admin_panel" />

                        <Separator class="lg:col-span-2" />

                        <div class="space-y-3 lg:col-span-2">
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
                                        :default-checked="hasRole(user, role.id)"
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
                                    No roles defined.
                                </p>
                            </div>
                            <InputError :message="errors.role_ids" />
                        </div>

                        <div class="flex flex-wrap items-center gap-3 pt-2 lg:col-span-2">
                            <Button :disabled="processing" type="submit">
                                Update User
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
                                    User updated successfully
                                </span>
                            </Transition>
                        </div>
                    </CardContent>
                    </Form>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
