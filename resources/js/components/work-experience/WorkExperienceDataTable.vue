<script setup lang="ts">
import {
    FlexRender,
    getCoreRowModel,
    useVueTable,
} from '@tanstack/vue-table';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { getColumns } from './columns';
import type { AuthCan } from '@/types/auth';
import type { WorkExperience } from '@/types/work-experience';

const props = defineProps<{
    data: WorkExperience[];
    onDelete: (workExperience: WorkExperience) => void;
}>();

const page = usePage();
const can = computed(() => ({
    ...((page.props.auth as { can?: Partial<AuthCan> })?.can ?? {}),
    ...((page.props as { can?: Partial<AuthCan> }).can ?? {}),
}));
const columns = computed(() => getColumns(props.onDelete, can.value));

const table = useVueTable({
    get data() {
        return props.data;
    },
    get columns() {
        return columns.value;
    },
    getCoreRowModel: getCoreRowModel(),
});
</script>

<template>
    <div class="rounded-md border">
        <Table>
            <TableHeader>
                <TableRow
                    v-for="headerGroup in table.getHeaderGroups()"
                    :key="headerGroup.id"
                >
                    <TableHead
                        v-for="header in headerGroup.headers"
                        :key="header.id"
                        :colspan="header.colSpan"
                    >
                        <FlexRender
                            v-if="header.column.columnDef.header"
                            :render="header.column.columnDef.header"
                            :props="header.getContext()"
                        />
                    </TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <template v-if="table.getRowModel().rows?.length">
                    <TableRow
                        v-for="row in table.getRowModel().rows"
                        :key="row.id"
                        :data-state="row.getIsSelected() ? 'selected' : undefined"
                    >
                        <TableCell
                            v-for="cell in row.getVisibleCells()"
                            :key="cell.id"
                        >
                            <FlexRender
                                :render="cell.column.columnDef.cell"
                                :props="cell.getContext()"
                            />
                        </TableCell>
                    </TableRow>
                </template>
                <TableRow v-else>
                    <TableCell
                        :colspan="columns.length"
                        class="h-24 text-center"
                    >
                        No results.
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
