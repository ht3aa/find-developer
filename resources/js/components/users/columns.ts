import type { ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';
import { Link } from '@inertiajs/vue3';
import { MoreHorizontal, Pencil, Trash2 } from 'lucide-vue-next';
import UserController from '@/actions/App/Http/Controllers/Dashboard/UserController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { AuthCan } from '@/types/auth';
import type { UserTableRow } from '@/types/user';

export function getColumns(
    onDelete: (user: UserTableRow) => void,
    can: Partial<AuthCan> = {},
    currentUserId?: number,
): ColumnDef<UserTableRow>[] {
    return [
        {
            accessorKey: 'name',
            header: 'Name',
            cell: ({ row }) =>
                h('div', { class: 'font-medium' }, row.original.name),
        },
        {
            accessorKey: 'email',
            header: 'Email',
            cell: ({ row }) =>
                h('div', { class: 'text-muted-foreground' }, row.original.email),
        },
        {
            accessorKey: 'user_type_label',
            header: 'Type',
            cell: ({ row }) =>
                h(Badge, { variant: 'secondary' }, () => row.original.user_type_label),
        },
        {
            accessorKey: 'can_access_admin_panel',
            header: 'Admin',
            cell: ({ row }) =>
                h(
                    Badge,
                    {
                        variant: row.original.can_access_admin_panel ? 'default' : 'secondary',
                    },
                    () => (row.original.can_access_admin_panel ? 'Yes' : 'No'),
                ),
        },
        {
            accessorKey: 'roles',
            header: 'Roles',
            cell: ({ row }) => {
                const roles = row.original.roles;
                if (!roles?.length) {
                    return h('span', { class: 'text-muted-foreground' }, '—');
                }
                return h(
                    'div',
                    { class: 'flex flex-wrap gap-1' },
                    roles.map((r) =>
                        h(Badge, { key: r.id, variant: 'outline', class: 'text-xs' }, () => r.name),
                    ),
                );
            },
        },
        {
            id: 'actions',
            header: '',
            cell: ({ row }) => {
                const showEdit = can.updateUser !== false;
                const showDelete =
                    can.deleteUser !== false && row.original.id !== currentUserId;
                if (!showEdit && !showDelete) return null;
                const items = [
                    showEdit
                        ? h(
                              DropdownMenuItem,
                              { asChild: true },
                              () =>
                                  h(
                                      Link,
                                      { href: UserController.edit.url(row.original.id) },
                                      () => [
                                          h(Pencil, { class: 'mr-2 h-4 w-4' }),
                                          'Edit',
                                      ],
                                  ),
                          )
                        : null,
                    showDelete
                        ? h(
                              DropdownMenuItem,
                              {
                                  class: 'text-destructive focus:text-destructive',
                                  onClick: () => onDelete(row.original),
                              },
                              () => [
                                  h(Trash2, { class: 'mr-2 h-4 w-4' }),
                                  'Delete',
                              ],
                          )
                        : null,
                ].filter(Boolean);
                return h(DropdownMenu, null, {
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
                        h(DropdownMenuContent, { align: 'end' }, () => items),
                    ],
                });
            },
        },
    ];
}
