<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { MoreHorizontal, Pencil, Trash2 } from 'lucide-vue-next';
import BadgeController from '@/actions/App/Http/Controllers/BadgeController';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { AuthCan } from '@/types/auth';
import type { Badge } from '@/types/badge';

const props = defineProps<{
    badge: Badge;
    onDelete: (badge: Badge) => void;
    can?: Partial<AuthCan>;
}>();

const showEdit = () => props.can?.updateBadge !== false;
const showDelete = () => props.can?.deleteBadge !== false;
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
                <Link :href="BadgeController.edit.url(badge.id)">
                    <Pencil class="mr-2 h-4 w-4" />
                    Edit
                </Link>
            </DropdownMenuItem>
            <DropdownMenuItem
                v-if="showDelete()"
                class="text-destructive focus:text-destructive"
                @click="onDelete(badge)"
            >
                <Trash2 class="mr-2 h-4 w-4" />
                Delete
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
