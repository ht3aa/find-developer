<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import {
    FlexRender,
    getCoreRowModel,
    useVueTable,
} from '@tanstack/vue-table';
import { Mail } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { getColumns, type SubscriberRow } from './columns';

const props = withDefaults(
    defineProps<{
        data: SubscriberRow[];
        from: number | null;
        bulkEmailUrl?: string;
    }>(),
    { bulkEmailUrl: '' },
);

function formatDate(iso: string): string {
    const d = new Date(iso);
    return d.toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

const rowIndexOffset = computed(() => (props.from != null ? props.from - 1 : 0));
const columns = computed(() => getColumns(rowIndexOffset.value, formatDate));

const rowSelection = ref<Record<string, boolean>>({});

const table = useVueTable({
    get data() {
        return props.data;
    },
    get columns() {
        return columns.value;
    },
    getCoreRowModel: getCoreRowModel(),
    getRowId: (row: SubscriberRow) => String(row.id),
    state: {
        get rowSelection() {
            return rowSelection.value;
        },
    },
    onRowSelectionChange: (updaterOrValue) => {
        rowSelection.value =
            typeof updaterOrValue === 'function' ? updaterOrValue(rowSelection.value) : updaterOrValue;
    },
    enableRowSelection: true,
});

const selectedRows = computed(() => {
    void rowSelection.value;
    return table.getSelectedRowModel().rows;
});
const selectedCount = computed(() => selectedRows.value.length);
const hasSelection = computed(() => selectedCount.value > 0);

const bulkEmailOpen = ref(false);
const bulkEmailForm = ref({
    title: '',
    body: '',
    category: '',
});
const bulkEmailSubmitting = ref(false);

function openBulkEmailDialog() {
    bulkEmailForm.value = { title: '', body: '', category: '' };
    bulkEmailOpen.value = true;
}

function submitBulkEmail() {
    if (!props.bulkEmailUrl) return;
    const ids = selectedRows.value.map((r: { original: SubscriberRow }) => r.original.id);
    bulkEmailSubmitting.value = true;
    router.post(props.bulkEmailUrl, {
        subscriber_ids: ids,
        title: bulkEmailForm.value.title,
        body: bulkEmailForm.value.body,
        category: bulkEmailForm.value.category,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            bulkEmailOpen.value = false;
            rowSelection.value = {};
        },
        onFinish: () => {
            bulkEmailSubmitting.value = false;
        },
    });
}
</script>

<template>
    <div class="space-y-3">
        <div
            v-if="hasSelection"
            class="flex flex-wrap items-center gap-3 rounded-md border bg-muted/50 px-4 py-2"
        >
            <span class="text-muted-foreground text-sm">
                {{ selectedCount }} of {{ table.getRowModel().rows.length }} row(s) selected.
            </span>
            <Button
                variant="secondary"
                size="sm"
                @click="openBulkEmailDialog"
            >
                <Mail class="mr-2 h-4 w-4" />
                Send Mailtrap email
            </Button>
        </div>

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
                            :class="header.id === 'index' ? 'w-[80px]' : header.id === 'subscribed_at' ? 'w-[180px]' : undefined"
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

        <Dialog v-model:open="bulkEmailOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Send Mailtrap email</DialogTitle>
                    <DialogDescription>
                        Send an email to each selected subscriber. Title is used as the email body heading.
                    </DialogDescription>
                </DialogHeader>
                <form
                    class="grid gap-4 py-4"
                    @submit.prevent="submitBulkEmail"
                >
                    <div class="grid gap-2">
                        <Label for="bulk-email-selected-title">Title</Label>
                        <Input
                            id="bulk-email-selected-title"
                            v-model="bulkEmailForm.title"
                            placeholder="Email title / heading"
                            required
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="bulk-email-selected-body">Body</Label>
                            <textarea
                                id="bulk-email-selected-body"
                                v-model="bulkEmailForm.body"
                                rows="3"
                                placeholder="Email body content"
                            required
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="bulk-email-selected-category">Category</Label>
                        <Input
                            id="bulk-email-selected-category"
                            v-model="bulkEmailForm.category"
                            placeholder="e.g. Newsletter, Notification"
                        />
                    </div>
                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            @click="bulkEmailOpen = false"
                        >
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            :disabled="bulkEmailSubmitting"
                        >
                            {{ bulkEmailSubmitting ? 'Sending…' : 'Send' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>
