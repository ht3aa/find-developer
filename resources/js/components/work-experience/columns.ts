import type { ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';
import { Badge } from '@/components/ui/badge';
import WorkExperienceActionsCell from '@/components/work-experience/WorkExperienceActionsCell.vue';
import type { AuthCan } from '@/types/auth';
import type { WorkExperience } from '@/types/work-experience';

function formatDateRange(startDate: string, endDate: string | null, isCurrent: boolean): string {
    const start = new Date(startDate).getFullYear();
    if (isCurrent || !endDate) {
        return `${start} – Present`;
    }
    const end = new Date(endDate).getFullYear();
    return `${start} – ${end}`;
}

export function getColumns(
    onDelete: (workExperience: WorkExperience) => void,
    can: Partial<AuthCan> = {},
): ColumnDef<WorkExperience>[] {
    return [
        {
            accessorKey: 'company_name',
            header: 'Company',
            cell: ({ row }) =>
                h('div', { class: 'font-medium' }, row.original.company_name),
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
            id: 'promotion_from',
            header: 'Promotion From',
            cell: ({ row }) => {
                const parent = row.original.parent;
                if (!parent) return h('div', { class: 'text-muted-foreground' }, '—');
                return h(
                    'div',
                    { class: 'text-muted-foreground text-sm' },
                    `${parent.job_title ?? 'N/A'} (${parent.company_name})`,
                );
            },
        },
        {
            id: 'date_range',
            header: 'Period',
            cell: ({ row }) =>
                h(
                    'div',
                    { class: 'text-muted-foreground' },
                    formatDateRange(
                        row.original.start_date,
                        row.original.end_date,
                        row.original.is_current,
                    ),
                ),
        },
        {
            accessorKey: 'show_company',
            header: 'Visible',
            cell: ({ row }) =>
                h(
                    Badge,
                    {
                        variant: row.original.show_company ? 'default' : 'secondary',
                    },
                    () => (row.original.show_company ? 'Yes' : 'No'),
                ),
        },
        {
            id: 'actions',
            header: '',
            cell: ({ row }) =>
                h(WorkExperienceActionsCell, {
                    workExperience: row.original,
                    onDelete,
                    can,
                }),
        },
    ];
}
