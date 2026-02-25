import type { ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';
import { Link } from '@inertiajs/vue3';
import { MoreHorizontal, Pencil, Trash2 } from 'lucide-vue-next';
import RoleController from '@/actions/App/Http/Controllers/Dashboard/RoleController';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { Role } from '@/types/role';

export function getColumns(onDelete: (role: Role) => void): ColumnDef<Role>[] {
    return [
        {
            accessorKey: 'name',
            header: 'Name',
            cell: ({ row }) =>
                h('div', { class: 'font-medium' }, row.original.name),
        },
        {
            accessorKey: 'guard_name',
            header: 'Guard',
            cell: ({ row }) =>
                h(
                    'div',
                    { class: 'text-muted-foreground font-mono text-sm' },
                    row.original.guard_name,
                ),
        },
        {
            accessorKey: 'users_count',
            header: 'Users',
            cell: ({ row }) =>
                h(
                    'span',
                    { class: 'text-muted-foreground' },
                    String(row.original.users_count ?? 0),
                ),
        },
        {
            id: 'actions',
            header: '',
            cell: ({ row }) =>
                h(DropdownMenu, null, {
                    default: () => [
                        h(DropdownMenuTrigger, { asChild: true }, () =>
                            h(
                                Button,
                                { variant: 'ghost', size: 'icon', class: 'h-8 w-8' },
                                () => [
                                    h('span', { class: 'sr-only' }, 'Open menu'),
                                    h(MoreHorizontal, { class: 'h-4 w-4' }),
                                ],
                            ),
                        ),
                        h(DropdownMenuContent, { align: 'end' }, () => [
                            h(
                                DropdownMenuItem,
                                { asChild: true },
                                () =>
                                    h(
                                        Link,
                                        { href: RoleController.edit.url(row.original.id) },
                                        () => [
                                            h(Pencil, { class: 'mr-2 h-4 w-4' }),
                                            'Edit',
                                        ],
                                    ),
                            ),
                            h(
                                DropdownMenuItem,
                                {
                                    class: 'text-destructive focus:text-destructive',
                                    onClick: () => onDelete(row.original),
                                },
                                () => [
                                    h(Trash2, { class: 'mr-2 h-4 w-4' }),
                                    'Delete',
                                ],
                            ),
                        ]),
                    ],
                }),
        },
    ];
}
