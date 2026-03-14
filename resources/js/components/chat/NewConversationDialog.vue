<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Loader2, MessageSquarePlus, Search } from 'lucide-vue-next';
import { ref } from 'vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { getInitials } from '@/composables/useChat';
import type { ChatUserSummary } from '@/types';

const open = ref(false);
const searchQuery = ref('');
const results = ref<ChatUserSummary[]>([]);
const isSearching = ref(false);
const isStarting = ref(false);

let searchTimeout: ReturnType<typeof setTimeout> | null = null;

function onSearch() {
    if (searchTimeout) clearTimeout(searchTimeout);

    if (searchQuery.value.length < 2) {
        results.value = [];
        return;
    }

    isSearching.value = true;
    searchTimeout = setTimeout(async () => {
        try {
            const response = await fetch(
                `/messages/search-users?q=${encodeURIComponent(searchQuery.value)}`,
                {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                },
            );
            results.value = await response.json();
        } catch {
            results.value = [];
        } finally {
            isSearching.value = false;
        }
    }, 300);
}

function startConversation(user: ChatUserSummary) {
    isStarting.value = true;
    router.post(
        '/messages',
        {
            recipient_id: user.id,
            body: '',
        },
        {
            onFinish: () => {
                isStarting.value = false;
                open.value = false;
                searchQuery.value = '';
                results.value = [];
            },
        },
    );
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <Button variant="outline" size="sm" class="gap-2">
                <MessageSquarePlus class="size-4" />
                <span class="hidden sm:inline">New Chat</span>
            </Button>
        </DialogTrigger>

        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>New Conversation</DialogTitle>
                <DialogDescription>
                    Search for a user to start chatting with.
                </DialogDescription>
            </DialogHeader>

            <div class="space-y-4">
                <div class="relative">
                    <Search
                        class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="searchQuery"
                        placeholder="Search by name or email..."
                        class="pl-9"
                        @input="onSearch"
                    />
                </div>

                <div class="max-h-[300px] overflow-y-auto">
                    <div
                        v-if="isSearching"
                        class="flex items-center justify-center py-8"
                    >
                        <Loader2
                            class="size-5 animate-spin text-muted-foreground"
                        />
                    </div>

                    <div
                        v-else-if="
                            searchQuery.length >= 2 && results.length === 0
                        "
                        class="py-8 text-center text-sm text-muted-foreground"
                    >
                        No users found
                    </div>

                    <div
                        v-else-if="
                            searchQuery.length < 2 && results.length === 0
                        "
                        class="py-8 text-center text-sm text-muted-foreground"
                    >
                        Type at least 2 characters to search
                    </div>

                    <button
                        v-for="user in results"
                        :key="user.id"
                        class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left transition-colors hover:bg-accent"
                        :disabled="isStarting"
                        @click="startConversation(user)"
                    >
                        <Avatar class="size-9">
                            <AvatarFallback
                                class="bg-primary/10 text-xs font-medium text-primary"
                            >
                                {{ getInitials(user.name) }}
                            </AvatarFallback>
                        </Avatar>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">
                                {{ user.name }}
                            </p>
                            <p class="truncate text-xs text-muted-foreground">
                                {{ user.email }}
                            </p>
                        </div>
                    </button>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
