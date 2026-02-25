<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { MoreHorizontal, Pencil, Trash2 } from 'lucide-vue-next';
import WorkExperienceController from '@/actions/App/Http/Controllers/Dashboard/WorkExperienceController';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { WorkExperience } from '@/types/work-experience';

defineProps<{
    workExperience: WorkExperience;
    onDelete: (workExperience: WorkExperience) => void;
}>();
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" size="icon" class="h-8 w-8">
                <span class="sr-only">Open menu</span>
                <MoreHorizontal class="h-4 w-4" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuItem as-child>
                <Link :href="WorkExperienceController.edit.url(workExperience.id)">
                    <Pencil class="mr-2 h-4 w-4" />
                    Edit
                </Link>
            </DropdownMenuItem>
            <DropdownMenuItem
                class="text-destructive focus:text-destructive"
                @click="onDelete(workExperience)"
            >
                <Trash2 class="mr-2 h-4 w-4" />
                Delete
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
