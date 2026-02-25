import type { ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ExternalLink, Pencil } from 'lucide-vue-next';
import DeveloperController from '@/actions/App/Http/Controllers/Dashboard/DeveloperController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { AuthCan } from '@/types/auth';
import type { DeveloperTableRow } from '@/types/developer-table';

function statusVariant(status: string): 'default' | 'secondary' | 'destructive' | 'outline' {
    return status === 'approved'
        ? 'default'
        : status === 'rejected'
          ? 'destructive'
          : 'secondary';
}

export function getColumns(can: Partial<AuthCan> = {}): ColumnDef<DeveloperTableRow>[] {
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
                h(
                    'div',
                    { class: 'text-muted-foreground' },
                    row.original.email,
                ),
        },
        {
            accessorKey: 'job_title',
            header: 'Job Title',
            cell: ({ row }) =>
                h(
                    'div',
                    { class: 'text-muted-foreground' },
                    row.original.job_title ?? '—',
                ),
        },
        {
            accessorKey: 'years_of_experience',
            header: 'Experience',
            cell: ({ row }) =>
                h(
                    'span',
                    { class: 'text-muted-foreground' },
                    `${row.original.years_of_experience} yrs`,
                ),
        },
        {
            accessorKey: 'status',
            header: 'Status',
            cell: ({ row }) =>
                h(
                    Badge,
                    {
                        variant: statusVariant(row.original.status),
                    },
                    () => row.original.status_label,
                ),
        },
        {
            accessorKey: 'is_available',
            header: 'Available',
            cell: ({ row }) =>
                h(
                    Badge,
                    {
                        variant: row.original.is_available ? 'default' : 'secondary',
                    },
                    () => (row.original.is_available ? 'Yes' : 'No'),
                ),
        },
        {
            id: 'actions',
            header: '',
            cell: ({ row }) => {
                const editUrl = DeveloperController.edit.url(row.original.id);
                const viewUrl = row.original.profile_url;
                const showEdit = can.updateDeveloper !== false;
                const showView = can.viewDeveloper !== false && viewUrl;
                const nodes = [
                    showEdit
                        ? h(
                              Button,
                              {
                                  variant: 'ghost',
                                  size: 'icon',
                                  class: 'h-8 w-8',
                                  asChild: true,
                              },
                              () =>
                                  h(
                                      Link,
                                      {
                                          href: editUrl,
                                          'aria-label': `Edit ${row.original.name}`,
                                      },
                                      () => h(Pencil, { class: 'h-4 w-4' }),
                                  ),
                          )
                        : null,
                    showView
                        ? h(
                              Button,
                              {
                                  variant: 'ghost',
                                  size: 'icon',
                                  class: 'h-8 w-8',
                                  asChild: true,
                              },
                              () =>
                                  h(
                                      Link,
                                      {
                                          href: viewUrl,
                                          target: '_blank',
                                          rel: 'noopener noreferrer',
                                          'aria-label': `View ${row.original.name}`,
                                      },
                                      () => h(ExternalLink, { class: 'h-4 w-4' }),
                                  ),
                          )
                        : null,
                ].filter(Boolean);
                if (nodes.length === 0) return null;
                return h('div', { class: 'flex items-center gap-1' }, nodes);
            },
        },
    ];
}
