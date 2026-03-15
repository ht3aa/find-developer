<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { refDebounced } from '@vueuse/core';
import { ChevronDown, Mail, Search } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import NewsletterDataTable from '@/components/newsletter/NewsletterDataTable.vue';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as newsletterIndex } from '@/routes/dashboard/newsletter';
import type { BreadcrumbItem } from '@/types';

type Subscriber = {
    id: number;
    email: string;
    subscribed_at: string;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type Props = {
    subscribers: {
        data: Subscriber[];
        links: PaginationLink[];
        current_page: number;
        last_page: number;
        total: number;
        from: number | null;
        to: number | null;
    };
    filters?: { search?: string };
    bulkEmailUrl?: string;
    bulkEmailAllUrl?: string;
};

const props = withDefaults(defineProps<Props>(), {
    filters: () => ({ search: '' }),
    bulkEmailUrl: undefined,
    bulkEmailAllUrl: undefined,
});

const searchQuery = ref(props.filters.search ?? '');
const debouncedSearch = refDebounced(searchQuery, 300);

watch(debouncedSearch, (value) => {
    router.get(newsletterIndex().url, { search: value || undefined }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});

const page = usePage();
const flash = computed(
    () => page.props.flash as { success?: string; error?: string } | undefined,
);

const bulkEmailOpen = ref(false);
const bulkEmailForm = ref({ title: '', body: '', category: '' });
const bulkEmailSubmitting = ref(false);

function openBulkEmailDialog() {
    bulkEmailForm.value = { title: '', body: '', category: '' };
    bulkEmailOpen.value = true;
}

function submitBulkEmailAll() {
    if (!props.bulkEmailAllUrl) return;
    bulkEmailSubmitting.value = true;
    router.post(
        props.bulkEmailAllUrl,
        {
            title: bulkEmailForm.value.title,
            body: bulkEmailForm.value.body,
            category: bulkEmailForm.value.category,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                bulkEmailOpen.value = false;
            },
            onFinish: () => {
                bulkEmailSubmitting.value = false;
            },
        },
    );
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Newsletter', href: newsletterIndex().url },
];
</script>

<template>
    <Head title="Newsletter" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div
                v-if="flash?.success"
                class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:border-green-800 dark:bg-green-950/50 dark:text-green-200"
            >
                {{ flash.success }}
            </div>
            <div
                v-if="flash?.error"
                class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 dark:border-red-800 dark:bg-red-950/50 dark:text-red-200"
            >
                {{ flash.error }}
            </div>
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Newsletter
                    </h1>
                    <p class="text-muted-foreground">
                        Subscriber emails (super admin only)
                    </p>
                </div>
                <div v-if="bulkEmailAllUrl" class="flex items-center gap-2">
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="outline">
                                Bulk actions
                                <ChevronDown class="ml-2 h-4 w-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem @select="openBulkEmailDialog">
                                <Mail class="mr-2 h-4 w-4" />
                                Send Mailtrap email
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>

            <div
                v-if="subscribers.data.length > 0 || (filters?.search ?? '')"
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="relative flex-1 sm:max-w-md">
                    <Search
                        class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Search by email..."
                        class="pl-9"
                        aria-label="Search subscribers"
                    />
                </div>
                <p
                    v-if="subscribers.total > 0"
                    class="text-sm text-muted-foreground"
                >
                    Showing {{ subscribers.from }}–{{ subscribers.to }} of
                    {{ subscribers.total }}
                </p>
            </div>

            <Dialog v-model:open="bulkEmailOpen">
                <DialogContent class="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>Send Mailtrap email</DialogTitle>
                        <DialogDescription>
                            Send an email to all newsletter subscribers. Title
                            is used as the email body heading.
                        </DialogDescription>
                    </DialogHeader>
                    <form
                        class="grid gap-4 py-4"
                        @submit.prevent="submitBulkEmailAll"
                    >
                        <div class="grid gap-2">
                            <Label for="bulk-email-title">Title</Label>
                            <Input
                                id="bulk-email-title"
                                v-model="bulkEmailForm.title"
                                placeholder="Email title / heading"
                                required
                            />
                        </div>
                        <div class="grid gap-2">
                            <Label for="bulk-email-body">Body</Label>
                            <textarea
                                id="bulk-email-body"
                                v-model="bulkEmailForm.body"
                                rows="3"
                                placeholder="Email body content"
                                required
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none"
                            />
                        </div>
                        <div class="grid gap-2">
                            <Label for="bulk-email-category">Category</Label>
                            <Input
                                id="bulk-email-category"
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

            <NewsletterDataTable
                v-if="subscribers.data.length > 0 || (filters?.search ?? '')"
                :data="subscribers.data"
                :from="subscribers.from"
                :bulk-email-url="bulkEmailUrl"
            />

            <div
                v-else-if="(filters?.search ?? '')"
                class="flex flex-col items-center justify-center rounded-xl border border-dashed py-12"
            >
                <Search class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No subscribers found</h3>
                <p class="text-center text-sm text-muted-foreground">
                    Try adjusting your search.
                </p>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center rounded-xl border border-dashed py-12"
            >
                <Mail class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No subscribers yet</h3>
                <p class="text-center text-sm text-muted-foreground">
                    Newsletter signups from the hero section will appear here.
                </p>
            </div>

            <Pagination
                v-if="subscribers.links?.length"
                :links="subscribers.links"
            />
        </div>
    </AppLayout>
</template>
