<script setup lang="ts">
import { useDebounceFn } from '@vueuse/core';
import { Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';

type DeveloperSearchItem = {
    id: number;
    name: string;
    profile_url: string | null;
    job_title?: { name?: string };
};

const open = defineModel<boolean>('open', { default: false });

const emit = defineEmits<{
    select: [payload: { profileUrl: string; displayName: string }];
}>();

const searchQuery = ref('');
const results = ref<DeveloperSearchItem[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);

async function fetchDevelopers(search: string): Promise<void> {
    loading.value = true;
    error.value = null;
    try {
        const url = new URL('/api/developers', window.location.origin);
        url.searchParams.set('per_page', '20');
        const q = search.trim();
        if (q.length > 0) {
            url.searchParams.set('filter[search]', q);
        }
        const response = await fetch(url.toString(), {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        if (!response.ok) {
            throw new Error('Request failed');
        }
        const json = (await response.json()) as {
            data?: DeveloperSearchItem[];
        };
        results.value = Array.isArray(json.data) ? json.data : [];
    } catch {
        error.value = 'Could not load developers. Try again.';
        results.value = [];
    } finally {
        loading.value = false;
    }
}

const debouncedSearch = useDebounceFn((q: string) => {
    void fetchDevelopers(q);
}, 300);

watch(searchQuery, (q) => {
    debouncedSearch(q);
});

watch(open, (isOpen) => {
    if (isOpen) {
        searchQuery.value = '';
        error.value = null;
        void fetchDevelopers('');
    }
});

function pick(developer: DeveloperSearchItem): void {
    const profileUrl = developer.profile_url;
    if (!profileUrl) {
        return;
    }
    emit('select', {
        profileUrl,
        displayName: developer.name,
    });
    open.value = false;
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Mention a developer</DialogTitle>
                <DialogDescription>
                    Search and insert a link to a developer profile in your
                    message.
                </DialogDescription>
            </DialogHeader>

            <div class="space-y-4">
                <div class="relative">
                    <Search
                        class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="searchQuery"
                        class="pl-9"
                        placeholder="Search by name, email, or skill..."
                        autocomplete="off"
                    />
                </div>

                <div
                    class="max-h-64 space-y-1 overflow-y-auto rounded-md border border-border p-1"
                >
                    <div
                        v-if="loading"
                        class="flex items-center justify-center gap-2 py-8 text-sm text-muted-foreground"
                    >
                        <Spinner class="size-4" />
                        Loading…
                    </div>
                    <p
                        v-else-if="error"
                        class="px-2 py-6 text-center text-sm text-destructive"
                    >
                        {{ error }}
                    </p>
                    <p
                        v-else-if="results.length === 0"
                        class="px-2 py-6 text-center text-sm text-muted-foreground"
                    >
                        No developers found.
                    </p>
                    <template v-else>
                        <Button
                            v-for="dev in results"
                            :key="dev.id"
                            type="button"
                            variant="ghost"
                            class="h-auto w-full justify-start gap-2 px-2 py-2 text-start font-normal"
                            :disabled="!dev.profile_url"
                            @click="pick(dev)"
                        >
                            <span class="min-w-0 flex-1">
                                <span class="block truncate font-medium">{{
                                    dev.name
                                }}</span>
                                <span
                                    v-if="dev.job_title?.name"
                                    class="block truncate text-xs text-muted-foreground"
                                >
                                    {{ dev.job_title.name }}
                                </span>
                            </span>
                        </Button>
                    </template>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
