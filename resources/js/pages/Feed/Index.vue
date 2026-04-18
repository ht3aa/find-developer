<script setup lang="ts">
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { Heart, ImagePlus, MessageCircle, Pencil, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import * as FeedController from '@/actions/App/Http/Controllers/FeedController';
import Footer from '@/components/Footer.vue';
import Navbar from '@/components/Navbar.vue';
import SeoHead from '@/components/SeoHead.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
} from '@/components/ui/card';
import { Textarea } from '@/components/ui/textarea';
import { show as developerShow } from '@/routes/developers';

type FeedUser = {
    id: number;
    name: string;
    developer: { slug: string; name: string } | null;
};

type FeedReply = {
    id: number;
    body: string;
    created_at: string;
    user: { id: number; name: string };
    can_delete: boolean;
};

type FeedComment = {
    id: number;
    body: string;
    created_at: string;
    user: { id: number; name: string };
    can_delete: boolean;
    replies: FeedReply[];
};

type FeedPostItem = {
    id: number;
    body: string;
    created_at: string;
    can: { update: boolean; delete: boolean };
    user: FeedUser;
    images: { id: number; url: string | null }[];
    likes_count: number;
    liked_by_me: boolean;
    comments: FeedComment[];
};

type PaginatorMeta = {
    current_page: number;
    last_page: number;
    next_page_url: string | null;
    prev_page_url: string | null;
    total: number;
};

defineProps<{
    posts: {
        data: FeedPostItem[];
        meta: PaginatorMeta;
    };
}>();

const page = usePage();
const authUser = computed(
    () =>
        page.props.auth as {
            user?: { id: number; name: string; email: string } | null;
        },
);
const flashSuccess = computed(
    () => (page.props.flash as { success?: string })?.success,
);
const appOgImage = computed(
    () => (page.props.appOgImage as string) ?? undefined,
);

const createForm = useForm({
    body: '',
    images: [] as File[],
});

const imageInputRef = ref<HTMLInputElement | null>(null);
const imagePreviewUrls = ref<string[]>([]);

function onImageFilesChange(e: Event): void {
    const input = e.target as HTMLInputElement;
    const files = input.files ? Array.from(input.files).slice(0, 4) : [];
    createForm.images = files;
    imagePreviewUrls.value.forEach((u: string) => URL.revokeObjectURL(u));
    imagePreviewUrls.value = files.map((f) => URL.createObjectURL(f));
}

function clearComposerImages(): void {
    createForm.images = [];
    imagePreviewUrls.value.forEach((u: string) => URL.revokeObjectURL(u));
    imagePreviewUrls.value = [];
    if (imageInputRef.value) {
        imageInputRef.value.value = '';
    }
}

function submitPost(): void {
    createForm.post(FeedController.store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            createForm.reset();
            clearComposerImages();
        },
    });
}

function toggleLike(post: FeedPostItem): void {
    if (!authUser.value?.user) return;
    router.post(
        FeedController.toggleLike.url({ feedPost: post.id }),
        {},
        { preserveScroll: true },
    );
}

const commentDraft = ref<Record<number, string>>({});
const replyParentId = ref<Record<number, number | null>>({});

function setCommentDraft(postId: number, value: string): void {
    commentDraft.value = { ...commentDraft.value, [postId]: value };
}

function setReplyTo(postId: number, parentId: number | null): void {
    replyParentId.value = { ...replyParentId.value, [postId]: parentId };
}

function submitComment(post: FeedPostItem): void {
    if (!authUser.value?.user) return;
    const body = (commentDraft.value[post.id] ?? '').trim();
    if (!body) return;
    const parentId = replyParentId.value[post.id] ?? null;
    router.post(
        FeedController.storeComment.url({ feedPost: post.id }),
        { body, parent_id: parentId },
        {
            preserveScroll: true,
            onSuccess: () => {
                commentDraft.value = { ...commentDraft.value, [post.id]: '' };
                setReplyTo(post.id, null);
            },
        },
    );
}

function deleteComment(commentId: number): void {
    if (!confirm('Delete this comment?')) return;
    router.delete(
        FeedController.destroyComment.url({ feedPostComment: commentId }),
        { preserveScroll: true },
    );
}

function deletePost(postId: number): void {
    if (!confirm('Delete this post?')) return;
    router.delete(FeedController.destroy.url({ feedPost: postId }), {
        preserveScroll: true,
    });
}

const editingPostId = ref<number | null>(null);
const editBody = ref('');
const editForm = useForm({
    body: '',
    images: [] as File[],
    clear_images: false,
});
const editImageInputRef = ref<HTMLInputElement | null>(null);
const editPreviewUrls = ref<string[]>([]);

function startEdit(post: FeedPostItem): void {
    editingPostId.value = post.id;
    editBody.value = post.body;
    editForm.body = post.body;
    editForm.images = [];
    editForm.clear_images = false;
    editPreviewUrls.value.forEach((u: string) => URL.revokeObjectURL(u));
    editPreviewUrls.value = [];
    if (editImageInputRef.value) editImageInputRef.value.value = '';
}

function cancelEdit(): void {
    editingPostId.value = null;
    editPreviewUrls.value.forEach((u: string) => URL.revokeObjectURL(u));
    editPreviewUrls.value = [];
}

function onEditImagesChange(e: Event): void {
    const input = e.target as HTMLInputElement;
    const files = input.files ? Array.from(input.files).slice(0, 4) : [];
    editForm.images = files;
    editForm.clear_images = files.length > 0;
    editPreviewUrls.value.forEach((u: string) => URL.revokeObjectURL(u));
    editPreviewUrls.value = files.map((f) => URL.createObjectURL(f));
}

function submitEdit(postId: number): void {
    editForm.body = editBody.value;
    editForm.patch(FeedController.update.url({ feedPost: postId }), {
        preserveScroll: true,
        onSuccess: () => cancelEdit(),
    });
}

function formatTime(iso: string): string {
    const d = new Date(iso);
    const now = new Date();
    const diff = (now.getTime() - d.getTime()) / 1000;
    if (diff < 60) return 'just now';
    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
    if (diff < 604800) return `${Math.floor(diff / 86400)}d ago`;
    return d.toLocaleDateString(undefined, {
        month: 'short',
        day: 'numeric',
        year: d.getFullYear() !== now.getFullYear() ? 'numeric' : undefined,
    });
}

/** One line per block so each line gets its own `dir="auto"` (mixed Arabic + English). */
function splitBodyLines(text: string): string[] {
    return text.replace(/\r\n/g, '\n').split('\n');
}

/** Classes for user-written text: logical alignment + Unicode bidi for mixed scripts. */
const mixedTextClass =
    'text-start [unicode-bidi:plaintext] [word-break:break-word]';
</script>

<template>
    <SeoHead
        title="Feed"
        description="Community posts from Find Developer members — share updates, images, and join the conversation."
        canonical="/feed"
        :image="appOgImage"
    />
    <Head title="Feed" />

    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-8">
            <h1 class="mb-6 text-2xl font-semibold tracking-tight">Feed</h1>

            <div
                v-if="flashSuccess"
                class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:border-green-800 dark:bg-green-950/50 dark:text-green-200"
            >
                {{ flashSuccess }}
            </div>

            <Card
                v-if="authUser?.user"
                class="mb-8 border-border"
            >
                <CardHeader class="pb-2">
                    <p class="text-sm font-medium text-muted-foreground">
                        Start a post
                    </p>
                </CardHeader>
                <CardContent class="space-y-3">
                    <Textarea
                        v-model="createForm.body"
                        dir="auto"
                        placeholder="What do you want to share?"
                        rows="4"
                        :class="['min-h-[100px] resize-y', mixedTextClass]"
                    />
                    <p
                        v-if="createForm.errors.body"
                        class="text-sm text-destructive"
                    >
                        {{ createForm.errors.body }}
                    </p>
                    <div
                        v-if="imagePreviewUrls.length"
                        class="flex flex-wrap gap-2"
                    >
                        <div
                            v-for="(url, i) in imagePreviewUrls"
                            :key="i"
                            class="relative h-20 w-20 overflow-hidden rounded-md border"
                        >
                            <img
                                :src="url"
                                alt=""
                                class="h-full w-full object-cover"
                            />
                        </div>
                    </div>
                </CardContent>
                <CardFooter class="flex flex-wrap items-center justify-between gap-2 border-t pt-4">
                    <div class="flex items-center gap-2">
                        <input
                            ref="imageInputRef"
                            type="file"
                            accept="image/jpeg,image/png,image/gif,image/webp"
                            multiple
                            class="hidden"
                            @change="onImageFilesChange"
                        />
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="imageInputRef?.click()"
                        >
                            <ImagePlus class="me-1 size-4" />
                            Images
                        </Button>
                        <Button
                            v-if="createForm.images.length"
                            type="button"
                            variant="ghost"
                            size="sm"
                            @click="clearComposerImages"
                        >
                            Clear images
                        </Button>
                    </div>
                    <Button
                        type="button"
                        :disabled="createForm.processing"
                        @click="submitPost"
                    >
                        Post
                    </Button>
                </CardFooter>
            </Card>

            <p
                v-else
                class="mb-8 rounded-lg border border-dashed border-border bg-muted/30 px-4 py-6 text-center text-sm text-muted-foreground"
            >
                <Link
                    href="/login"
                    class="font-medium text-primary underline underline-offset-2"
                    >Log in</Link
                >
                to share a post, like, and comment.
            </p>

            <div
                class="grid grid-cols-1 items-start gap-6 md:grid-cols-2 lg:grid-cols-3"
            >
                <Card
                    v-for="post in posts.data"
                    :key="post.id"
                    class="overflow-hidden border-border"
                >
                    <CardHeader class="flex flex-row items-start justify-between gap-2 space-y-0 pb-2">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <Link
                                    v-if="post.user.developer"
                                    :href="developerShow.url(post.user.developer.slug)"
                                    class="truncate font-semibold text-foreground hover:underline"
                                >
                                    {{
                                        post.user.developer.name ||
                                        post.user.name
                                    }}
                                </Link>
                                <span
                                    v-else
                                    class="truncate font-semibold"
                                    >{{ post.user.name }}</span
                                >
                            </div>
                            <p class="text-xs text-muted-foreground">
                                {{ formatTime(post.created_at) }}
                            </p>
                        </div>
                        <div
                            v-if="post.can.update || post.can.delete"
                            class="flex shrink-0 gap-1"
                        >
                            <Button
                                v-if="post.can.update"
                                variant="ghost"
                                size="icon"
                                class="size-8"
                                :aria-label="'Edit post'"
                                @click="startEdit(post)"
                            >
                                <Pencil class="size-4" />
                            </Button>
                            <Button
                                v-if="post.can.delete"
                                variant="ghost"
                                size="icon"
                                class="size-8 text-destructive hover:text-destructive"
                                :aria-label="'Delete post'"
                                @click="deletePost(post.id)"
                            >
                                <Trash2 class="size-4" />
                            </Button>
                        </div>
                    </CardHeader>

                    <CardContent class="space-y-3">
                        <template v-if="editingPostId === post.id">
                            <Textarea
                                v-model="editBody"
                                dir="auto"
                                rows="4"
                                :class="['min-h-[100px]', mixedTextClass]"
                            />
                            <p
                                v-if="editForm.errors.body"
                                class="text-sm text-destructive"
                            >
                                {{ editForm.errors.body }}
                            </p>
                            <input
                                ref="editImageInputRef"
                                type="file"
                                accept="image/jpeg,image/png,image/gif,image/webp"
                                multiple
                                class="hidden"
                                @change="onEditImagesChange"
                            />
                            <div class="flex flex-wrap gap-2">
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    @click="editImageInputRef?.click()"
                                >
                                    Replace images
                                </Button>
                            </div>
                            <div
                                v-if="editPreviewUrls.length"
                                class="flex flex-wrap gap-2"
                            >
                                <div
                                    v-for="(url, i) in editPreviewUrls"
                                    :key="i"
                                    class="relative h-16 w-16 overflow-hidden rounded border"
                                >
                                    <img
                                        :src="url"
                                        alt=""
                                        class="h-full w-full object-cover"
                                    />
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <Button
                                    size="sm"
                                    :disabled="editForm.processing"
                                    @click="submitEdit(post.id)"
                                >
                                    Save
                                </Button>
                                <Button
                                    size="sm"
                                    variant="ghost"
                                    @click="cancelEdit"
                                >
                                    Cancel
                                </Button>
                            </div>
                        </template>
                        <template v-else>
                            <div class="space-y-0 text-sm leading-relaxed">
                                <p
                                    v-for="(line, lineIdx) in splitBodyLines(
                                        post.body,
                                    )"
                                    :key="lineIdx"
                                    dir="auto"
                                    :class="[
                                        'min-h-[1.25em] whitespace-pre-wrap',
                                        mixedTextClass,
                                    ]"
                                >
                                    {{ line || '\u00a0' }}
                                </p>
                            </div>
                            <div
                                v-if="post.images.length"
                                class="grid grid-cols-2 gap-2 sm:grid-cols-2"
                            >
                                <a
                                    v-for="img in post.images"
                                    :key="img.id"
                                    :href="img.url ?? undefined"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="relative block aspect-video overflow-hidden rounded-lg border bg-muted"
                                >
                                    <img
                                        v-if="img.url"
                                        :src="img.url"
                                        alt=""
                                        class="h-full w-full object-cover"
                                    />
                                </a>
                            </div>
                        </template>
                    </CardContent>

                    <CardFooter
                        class="flex flex-col items-stretch gap-3 border-t pt-4"
                    >
                        <div class="flex items-center gap-4">
                            <Button
                                type="button"
                                variant="ghost"
                                size="sm"
                                class="gap-1.5"
                                :class="
                                    post.liked_by_me
                                        ? 'text-red-600 dark:text-red-400'
                                        : ''
                                "
                                :disabled="!authUser?.user"
                                @click="toggleLike(post)"
                            >
                                <Heart
                                    class="size-4"
                                    :fill="post.liked_by_me ? 'currentColor' : 'none'"
                                />
                                <span>{{ post.likes_count }}</span>
                            </Button>
                            <span
                                class="flex items-center gap-1 text-sm text-muted-foreground"
                            >
                                <MessageCircle class="size-4" />
                                {{ post.comments.length }}
                            </span>
                        </div>

                        <div
                            v-if="post.comments.length"
                            class="space-y-3 border-s-2 border-muted ps-3"
                        >
                            <div
                                v-for="c in post.comments"
                                :key="c.id"
                                class="text-sm"
                            >
                                <p
                                    class="font-medium text-foreground"
                                    dir="auto"
                                >
                                    {{ c.user.name }}
                                    <span class="font-normal text-muted-foreground">
                                        · {{ formatTime(c.created_at) }}
                                    </span>
                                </p>
                                <div class="mt-0.5 space-y-0 text-muted-foreground">
                                    <p
                                        v-for="(line, lineIdx) in splitBodyLines(
                                            c.body,
                                        )"
                                        :key="lineIdx"
                                        dir="auto"
                                        :class="[
                                            'min-h-[1.25em] whitespace-pre-wrap',
                                            mixedTextClass,
                                        ]"
                                    >
                                        {{ line || '\u00a0' }}
                                    </p>
                                </div>
                                <div class="mt-1 flex flex-wrap items-center gap-2">
                                    <Button
                                        v-if="authUser?.user"
                                        variant="link"
                                        class="h-auto p-0 text-xs"
                                        @click="setReplyTo(post.id, c.id)"
                                    >
                                        Reply
                                    </Button>
                                    <Button
                                        v-if="c.can_delete"
                                        variant="link"
                                        class="h-auto p-0 text-xs text-destructive"
                                        @click="deleteComment(c.id)"
                                    >
                                        Delete
                                    </Button>
                                </div>
                                <div
                                    v-if="c.replies.length"
                                    class="mt-2 ms-3 space-y-2 border-s border-border ps-3"
                                >
                                    <div
                                        v-for="r in c.replies"
                                        :key="r.id"
                                    >
                                        <p
                                            class="font-medium"
                                            dir="auto"
                                        >
                                            {{ r.user.name }}
                                            <span
                                                class="font-normal text-muted-foreground"
                                            >
                                                · {{ formatTime(r.created_at) }}
                                            </span>
                                        </p>
                                        <div
                                            class="mt-0.5 space-y-0 text-muted-foreground"
                                        >
                                            <p
                                                v-for="(
                                                    line, lineIdx
                                                ) in splitBodyLines(r.body)"
                                                :key="lineIdx"
                                                dir="auto"
                                                :class="[
                                                    'min-h-[1.25em] whitespace-pre-wrap',
                                                    mixedTextClass,
                                                ]"
                                            >
                                                {{ line || '\u00a0' }}
                                            </p>
                                        </div>
                                        <Button
                                            v-if="r.can_delete"
                                            variant="link"
                                            class="mt-1 h-auto p-0 text-xs text-destructive"
                                            @click="deleteComment(r.id)"
                                        >
                                            Delete
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="authUser?.user"
                            class="space-y-1"
                        >
                            <p
                                v-if="replyParentId[post.id]"
                                class="text-xs text-muted-foreground"
                            >
                                Replying to thread
                                <Button
                                    variant="link"
                                    class="h-auto p-0 text-xs"
                                    @click="setReplyTo(post.id, null)"
                                >
                                    Cancel
                                </Button>
                            </p>
                            <div class="flex gap-2">
                                <Textarea
                                    :model-value="commentDraft[post.id] ?? ''"
                                    dir="auto"
                                    :placeholder="'Write a comment…'"
                                    rows="2"
                                    :class="[
                                        'min-h-[60px] flex-1 resize-y text-sm',
                                        mixedTextClass,
                                    ]"
                                    @update:model-value="
                                        (v) => setCommentDraft(post.id, v)
                                    "
                                />
                                <Button
                                    type="button"
                                    size="sm"
                                    class="self-end"
                                    @click="submitComment(post)"
                                >
                                    Send
                                </Button>
                            </div>
                        </div>
                    </CardFooter>
                </Card>

                <p
                    v-if="!posts.data.length"
                    class="col-span-full py-12 text-center text-muted-foreground"
                >
                    No posts yet. Be the first to share something.
                </p>
            </div>

            <div
                v-if="posts.meta.last_page > 1"
                class="mt-8 flex justify-center gap-4"
            >
                <Link
                    v-if="posts.meta.prev_page_url"
                    :href="posts.meta.prev_page_url"
                    class="inline-flex h-9 items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground"
                >
                    Previous
                </Link>
                <span class="self-center text-sm text-muted-foreground">
                    Page {{ posts.meta.current_page }} of
                    {{ posts.meta.last_page }}
                </span>
                <Link
                    v-if="posts.meta.next_page_url"
                    :href="posts.meta.next_page_url"
                    class="inline-flex h-9 items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground"
                >
                    Next
                </Link>
            </div>
        </main>

        <Footer />
    </div>
</template>
