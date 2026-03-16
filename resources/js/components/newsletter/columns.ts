import type { ColumnDef } from '@tanstack/vue-table';
import { MoreHorizontal, Trash2 } from 'lucide-vue-next';
import { h } from 'vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

export type SubscriberRow = {
    id: number;
    email: string;
    subscribed_at: string;
    delete_url: string;
};

const checkboxClass =
    'size-4 shrink-0 rounded-[4px] border border-input accent-primary focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50';

export function getColumns(
    rowIndexOffset: number,
    formatDate: (iso: string) => string,
    onDelete: (row: SubscriberRow) => void,
): ColumnDef<SubscriberRow>[] {
    return [
        {
            id: 'select',
            header: ({ table }) => {
                const checked = table.getIsAllRowsSelected();
                const indeterminate = table.getIsSomeRowsSelected() && !checked;
                return h('input', {
                    type: 'checkbox',
                    class: checkboxClass,
                    checked,
                    ref: (el: unknown) => {
                        const input = el as HTMLInputElement | null;
                        if (input) input.indeterminate = indeterminate;
                    },
                    'aria-label': 'Select all',
                    onChange: (e: Event) => {
                        const target = e.target as HTMLInputElement;
                        table.toggleAllRowsSelected(target.checked);
                    },
                });
            },
            cell: ({ row }) =>
                h('input', {
                    type: 'checkbox',
                    class: checkboxClass,
                    checked: row.getIsSelected(),
                    'aria-label': 'Select row',
                    onChange: (e: Event) => {
                        const target = e.target as HTMLInputElement;
                        row.toggleSelected(target.checked);
                    },
                }),
            enableSorting: false,
            enableHiding: false,
        },
        {
            id: 'index',
            header: '#',
            cell: ({ row }) => {
                const index = row.index;
                return h(
                    'span',
                    { class: 'text-muted-foreground text-sm' },
                    String(rowIndexOffset + index + 1),
                );
            },
            enableSorting: false,
        },
        {
            accessorKey: 'email',
            header: 'Email',
            cell: ({ row }) =>
                h('div', { class: 'font-medium' }, row.original.email),
        },
        {
            accessorKey: 'subscribed_at',
            header: 'Subscribed at',
            cell: ({ row }) =>
                h(
                    'span',
                    {
                        class: 'text-muted-foreground text-sm whitespace-nowrap',
                    },
                    formatDate(row.original.subscribed_at),
                ),
            enableSorting: false,
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
                                {
                                    variant: 'ghost',
                                    size: 'icon',
                                    class: 'h-8 w-8',
                                },
                                () => [
                                    h(
                                        'span',
                                        { class: 'sr-only' },
                                        'Open menu',
                                    ),
                                    h(MoreHorizontal, { class: 'h-4 w-4' }),
                                ],
                            ),
                        ),
                        h(DropdownMenuContent, { align: 'end' }, () => [
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
            enableSorting: false,
        },
    ];
}
