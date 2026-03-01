import type { ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';

export type SubscriberRow = {
    id: number;
    email: string;
    subscribed_at: string;
};

const checkboxClass =
    'size-4 shrink-0 rounded-[4px] border border-input accent-primary focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50';

export function getColumns(
    rowIndexOffset: number,
    formatDate: (iso: string) => string,
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
                    { class: 'text-muted-foreground text-sm whitespace-nowrap' },
                    formatDate(row.original.subscribed_at),
                ),
            enableSorting: false,
        },
    ];
}
