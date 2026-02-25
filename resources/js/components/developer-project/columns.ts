import type { ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';
import { Badge } from '@/components/ui/badge';
import DeveloperProjectActionsCell from '@/components/developer-project/DeveloperProjectActionsCell.vue';
import type { AuthCan } from '@/types/auth';
import type { DeveloperProject } from '@/types/developer-project';

export function getColumns(
    onDelete: (project: DeveloperProject) => void,
    can: Partial<AuthCan> = {},
): ColumnDef<DeveloperProject>[] {
    return [
        {
            accessorKey: 'title',
            header: 'Title',
            cell: ({ row }) =>
                h('div', { class: 'font-medium' }, row.original.title),
        },
        {
            accessorKey: 'description',
            header: 'Description',
            cell: ({ row }) => {
                const desc = row.original.description;
                if (!desc) return h('div', { class: 'text-muted-foreground' }, '—');
                const truncated = desc.length > 80 ? `${desc.slice(0, 80)}…` : desc;
                return h('div', { class: 'max-w-md truncate text-muted-foreground text-sm' }, truncated);
            },
        },
        {
            accessorKey: 'link',
            header: 'Link',
            cell: ({ row }) => {
                const link = row.original.link;
                if (!link) return h('div', { class: 'text-muted-foreground' }, '—');
                return h(
                    'a',
                    {
                        class: 'text-primary hover:underline',
                        href: link,
                        target: '_blank',
                        rel: 'noopener noreferrer',
                    },
                    'View',
                );
            },
        },
        {
            accessorKey: 'show_project',
            header: 'Visible',
            cell: ({ row }) =>
                h(
                    Badge,
                    {
                        variant: row.original.show_project ? 'default' : 'secondary',
                    },
                    () => (row.original.show_project ? 'Yes' : 'No'),
                ),
        },
        {
            id: 'actions',
            header: '',
            cell: ({ row }) =>
                h(DeveloperProjectActionsCell, {
                    project: row.original,
                    onDelete,
                    can,
                }),
        },
    ];
}
