<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Shield } from 'lucide-vue-next';
import RoleController from '@/actions/App/Http/Controllers/Dashboard/RoleController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as rolesIndex, create as rolesCreate } from '@/routes/roles';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

type PermissionOption = {
    id: number;
    name: string;
    ability: string;
    ability_label: string;
};

type Props = {
    permissionsByResource: Record<string, PermissionOption[]>;
};

const props = defineProps<Props>();

const selectedPermissionIds = ref<number[]>([]);

const allPermissionIds = computed(() => {
    const ids: number[] = [];
    for (const perms of Object.values(props.permissionsByResource)) {
        for (const p of perms) ids.push(p.id);
    }
    return ids;
});

function permissionIdsForResource(resource: string): number[] {
    return (props.permissionsByResource[resource] ?? []).map((p) => p.id);
}

function checkAllPermissions(checked: boolean): void {
    selectedPermissionIds.value = checked ? [...allPermissionIds.value] : [];
}

function checkGroup(resource: string, checked: boolean): void {
    const ids = permissionIdsForResource(resource);
    if (checked) {
        const set = new Set(selectedPermissionIds.value);
        ids.forEach((id) => set.add(id));
        selectedPermissionIds.value = Array.from(set);
    } else {
        const removeSet = new Set(ids);
        selectedPermissionIds.value = selectedPermissionIds.value.filter((id) => !removeSet.has(id));
    }
}

function togglePermission(permId: number, checked: boolean): void {
    if (checked) {
        if (!selectedPermissionIds.value.includes(permId)) {
            selectedPermissionIds.value = [...selectedPermissionIds.value, permId];
        }
    } else {
        selectedPermissionIds.value = selectedPermissionIds.value.filter((id) => id !== permId);
    }
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Roles', href: rolesIndex().url },
    { title: 'Create', href: rolesCreate().url },
];
</script>

<template>
    <Head title="Create Role" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-4xl space-y-6 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10"
                >
                    <Shield class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <Heading
                        title="Create Role"
                        description="Add a new role (e.g. Admin, Editor)"
                    />
                </div>
            </div>

            <Card>
                <Form
                    :action="RoleController.store()"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <CardHeader class="pb-4">
                        <h3 class="text-sm font-medium text-muted-foreground">
                            Role details
                        </h3>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="grid gap-2">
                            <Label for="name">Name <span class="text-destructive">*</span></Label>
                            <Input
                                id="name"
                                name="name"
                                required
                                placeholder="e.g. admin, editor"
                                class="transition-colors focus-visible:ring-2"
                            />
                            <p class="text-xs text-muted-foreground">
                                Use lowercase, no spaces (e.g. admin, content-editor)
                            </p>
                            <InputError :message="errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="guard_name">Guard name</Label>
                            <Input
                                id="guard_name"
                                name="guard_name"
                                placeholder="web (default)"
                                class="font-mono transition-colors focus-visible:ring-2"
                            />
                            <p class="text-xs text-muted-foreground">
                                Usually "web" for web applications
                            </p>
                            <InputError :message="errors.guard_name" />
                        </div>

                        <Separator />

                        <div class="space-y-4">
                            <div class="flex flex-wrap items-center gap-4">
                                <h3 class="text-sm font-medium text-muted-foreground">
                                    Permissions (from policies)
                                </h3>
                                <div class="flex gap-2">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        @click="checkAllPermissions(true)"
                                    >
                                        Check all
                                    </Button>
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        @click="checkAllPermissions(false)"
                                    >
                                        Uncheck all
                                    </Button>
                                </div>
                            </div>
                            <p class="text-xs text-muted-foreground">
                                Assign policy-based permissions to this role. Users with this role will be able to perform the selected actions.
                            </p>
                            <template v-for="id in selectedPermissionIds" :key="`hidden-${id}`">
                                <input type="hidden" name="permission_ids[]" :value="id" />
                            </template>
                            <div
                                v-for="(permissions, resource) in permissionsByResource"
                                :key="resource"
                                class="space-y-2 rounded-lg border p-4"
                            >
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <h4 class="font-medium">{{ resource }}</h4>
                                    <div class="flex gap-1">
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="sm"
                                            class="h-7 text-xs"
                                            @click="checkGroup(resource, true)"
                                        >
                                            Check all
                                        </Button>
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="sm"
                                            class="h-7 text-xs"
                                            @click="checkGroup(resource, false)"
                                        >
                                            Uncheck all
                                        </Button>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-x-6 gap-y-2">
                                    <div
                                        v-for="perm in permissions"
                                        :key="perm.id"
                                        class="flex items-center space-x-2"
                                    >
                                        <input
                                            :id="`perm-${perm.id}`"
                                            type="checkbox"
                                            :checked="selectedPermissionIds.includes(perm.id)"
                                            class="h-4 w-4 rounded border-input accent-primary"
                                            @change="togglePermission(perm.id, ($event.target as HTMLInputElement).checked)"
                                        />
                                        <Label
                                            :for="`perm-${perm.id}`"
                                            class="text-sm font-normal cursor-pointer"
                                        >
                                            {{ perm.ability_label }}
                                        </Label>
                                    </div>
                                </div>
                            </div>
                            <p
                                v-if="Object.keys(permissionsByResource).length === 0"
                                class="text-sm text-muted-foreground"
                            >
                                No policy permissions found. Add can('Ability:Resource') in app/Policies to discover them.
                            </p>
                            <InputError :message="errors.permission_ids" />
                        </div>

                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <Button :disabled="processing" type="submit">
                                Create Role
                            </Button>
                            <Button variant="outline" as-child>
                                <Link :href="rolesIndex().url">Cancel</Link>
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
                                    Role created successfully
                                </span>
                            </Transition>
                        </div>
                    </CardContent>
                </Form>
            </Card>
        </div>
    </AppLayout>
</template>
