import type { ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';
import BadgeActionsCell from '@/components/badges/BadgeActionsCell.vue';
import { Badge as BadgeUi } from '@/components/ui/badge';
import type { AuthCan } from '@/types/auth';
import type { Badge as BadgeType } from '@/types/badge';

export function getColumns(
    onDelete: (badge: BadgeType) => void,
    can: Partial<AuthCan> = {},
): ColumnDef<BadgeType>[] {
    return [
        {
            accessorKey: 'name',
            header: 'Name',
            cell: ({ row }) =>
                h('div', { class: 'font-medium' }, row.original.name),
        },
        {
            accessorKey: 'description',
            header: 'Description',
            cell: ({ row }) =>
                h(
                    'div',
                    {
                        class: 'max-w-[300px] truncate text-muted-foreground',
                    },
                    row.original.description ?? '—',
                ),
        },
        {
            accessorKey: 'color',
            header: 'Color',
            cell: ({ row }) => {
                const color = row.original.color;
                if (color) {
                    return h(
                        BadgeUi,
                        {
                            variant: 'outline',
                            style: {
                                background: `${color}18`,
                                borderColor: `${color}50`,
                                color,
                            },
                        },
                        () => color,
                    );
                }
                return h(BadgeUi, { variant: 'secondary' }, () => '—');
            },
        },
        {
            accessorKey: 'is_active',
            header: 'Status',
            cell: ({ row }) =>
                h(
                    BadgeUi,
                    {
                        variant: row.original.is_active ? 'default' : 'secondary',
                    },
                    () => (row.original.is_active ? 'Active' : 'Inactive'),
                ),
        },
        {
            accessorKey: 'developers_count',
            header: 'Developers',
            cell: ({ row }) =>
                h(
                    'span',
                    { class: 'text-muted-foreground' },
                    String(row.original.developers_count ?? 0),
                ),
        },
        {
            id: 'actions',
            header: '',
            cell: ({ row }) =>
                h(BadgeActionsCell, {
                    badge: row.original,
                    onDelete,
                    can,
                }),
        },
    ];
}
