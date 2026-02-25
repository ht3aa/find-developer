<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { MoreHorizontal, Pencil, Trash2 } from 'lucide-vue-next';
import DeveloperProjectController from '@/actions/App/Http/Controllers/Dashboard/DeveloperProjectController';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { AuthCan } from '@/types/auth';
import type { DeveloperProject } from '@/types/developer-project';

const props = defineProps<{
    project: DeveloperProject;
    onDelete: (project: DeveloperProject) => void;
    can?: Partial<AuthCan>;
}>();

const showEdit = () => props.can?.updateDeveloperProject !== false;
const showDelete = () => props.can?.deleteDeveloperProject !== false;
const hasActions = () => showEdit() || showDelete();
</script>

<template>
    <DropdownMenu v-if="hasActions()">
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" size="icon" class="h-8 w-8">
                <span class="sr-only">Open menu</span>
                <MoreHorizontal class="h-4 w-4" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuItem v-if="showEdit()" as-child>
                <Link :href="DeveloperProjectController.edit.url(project.id)">
                    <Pencil class="mr-2 h-4 w-4" />
                    Edit
                </Link>
            </DropdownMenuItem>
            <DropdownMenuItem
                v-if="showDelete()"
                class="text-destructive focus:text-destructive"
                @click="onDelete(project)"
            >
                <Trash2 class="mr-2 h-4 w-4" />
                Delete
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
