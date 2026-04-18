<?php

namespace App\Http\Controllers;

use App\Http\Requests\Feed\StoreFeedPostCommentRequest;
use App\Http\Requests\Feed\StoreFeedPostRequest;
use App\Http\Requests\Feed\UpdateFeedPostRequest;
use App\Models\FeedPost;
use App\Models\FeedPostComment;
use App\Models\FeedPostImage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class FeedController extends Controller
{
    public function index(Request $request): Response
    {
        $query = FeedPost::query()
            ->withCount('likers')
            ->with([
                'user:id,name,email',
                'user.developer:id,user_id,slug,name',
                'images',
                'rootComments' => fn ($q) => $q->with(['user:id,name', 'replies.user:id,name'])->orderBy('created_at'),
            ])
            ->orderByDesc('created_at');

        if ($request->user()) {
            $query->withExists([
                'likers as liked_by_me' => fn ($q) => $q->where('feed_post_likes.user_id', $request->user()->id),
            ]);
        }

        $posts = $query->paginate(15)->withQueryString();

        return Inertia::render('Feed/Index', [
            'posts' => [
                'data' => collect($posts->items())->map(fn (FeedPost $post) => $this->postToArray($post, $request->user()))->values()->all(),
                'links' => $posts->linkCollection()->values()->toArray(),
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'from' => $posts->firstItem(),
                    'last_page' => $posts->lastPage(),
                    'path' => $posts->path(),
                    'per_page' => $posts->perPage(),
                    'to' => $posts->lastItem(),
                    'total' => $posts->total(),
                    'next_page_url' => $posts->nextPageUrl(),
                    'prev_page_url' => $posts->previousPageUrl(),
                ],
            ],
        ]);
    }

    public function store(StoreFeedPostRequest $request): RedirectResponse
    {
        $post = FeedPost::create([
            'user_id' => $request->user()->id,
            'body' => $request->validated('body'),
        ]);

        $this->storeImages($post, $request->file('images', []));

        return redirect()
            ->route('feed.index')
            ->with('success', 'Post published.');
    }

    public function update(UpdateFeedPostRequest $request, FeedPost $feedPost): RedirectResponse
    {
        $data = $request->validated();
        $feedPost->update(['body' => $data['body']]);

        if (! empty($data['clear_images'])) {
            $this->deleteAllImages($feedPost);
        }

        $files = $request->file('images', []);
        if ($files !== []) {
            $this->deleteAllImages($feedPost);
            $this->storeImages($feedPost, $files);
        }

        return redirect()
            ->route('feed.index')
            ->with('success', 'Post updated.');
    }

    public function destroy(Request $request, FeedPost $feedPost): RedirectResponse
    {
        $this->authorize('delete', $feedPost);

        $this->deleteAllImages($feedPost);
        $feedPost->delete();

        return redirect()
            ->route('feed.index')
            ->with('success', 'Post deleted.');
    }

    public function toggleLike(Request $request, FeedPost $feedPost): RedirectResponse
    {
        $user = $request->user();
        if ($feedPost->likers()->where('user_id', $user->id)->exists()) {
            $feedPost->likers()->detach($user->id);
        } else {
            $feedPost->likers()->attach($user->id);
        }

        return redirect()->back();
    }

    public function storeComment(StoreFeedPostCommentRequest $request, FeedPost $feedPost): RedirectResponse
    {
        $validated = $request->validated();
        $parentId = $validated['parent_id'] ?? null;

        if ($parentId !== null) {
            $parent = FeedPostComment::query()->findOrFail($parentId);
            if ($parent->feed_post_id !== $feedPost->id) {
                abort(422, 'Invalid parent comment.');
            }
            if ($parent->parent_id !== null) {
                abort(422, 'Reply only to top-level comments.');
            }
        }

        FeedPostComment::create([
            'feed_post_id' => $feedPost->id,
            'parent_id' => $parentId,
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        return redirect()->back();
    }

    public function destroyComment(Request $request, FeedPostComment $feedPostComment): RedirectResponse
    {
        $this->authorize('delete', $feedPostComment);

        $feedPostComment->delete();

        return redirect()->back();
    }

    /**
     * @param  array<int, UploadedFile>  $files
     */
    private function storeImages(FeedPost $post, array $files): void
    {
        foreach (array_values($files) as $index => $file) {
            if ($file === null) {
                continue;
            }
            $path = $file->store("feed-posts/{$post->id}", ['disk' => 's3']);
            FeedPostImage::create([
                'feed_post_id' => $post->id,
                'path' => $path,
                'sort_order' => $index,
            ]);
        }
    }

    private function deleteAllImages(FeedPost $post): void
    {
        foreach ($post->images as $image) {
            if ($image->path) {
                Storage::disk('s3')->delete($image->path);
            }
        }
        $post->images()->delete();
    }

    /**
     * @return array<string, mixed>
     */
    private function postToArray(FeedPost $post, ?User $authUser): array
    {
        $likedByMe = false;
        if ($authUser && isset($post->liked_by_me)) {
            $likedByMe = (bool) $post->liked_by_me;
        }

        return [
            'id' => $post->id,
            'body' => $post->body,
            'created_at' => $post->created_at->toIso8601String(),
            'can' => [
                'update' => $authUser ? $authUser->can('update', $post) : false,
                'delete' => $authUser ? $authUser->can('delete', $post) : false,
            ],
            'user' => [
                'id' => $post->user->id,
                'name' => $post->user->name,
                'developer' => $post->user->developer
                    ? [
                        'slug' => $post->user->developer->slug,
                        'name' => $post->user->developer->name,
                    ]
                    : null,
            ],
            'images' => $post->images->map(fn (FeedPostImage $img) => [
                'id' => $img->id,
                'url' => $img->url,
            ])->values()->all(),
            'likes_count' => (int) ($post->likers_count ?? 0),
            'liked_by_me' => $likedByMe,
            'comments' => $post->rootComments->map(function (FeedPostComment $c) use ($authUser) {
                return [
                    'id' => $c->id,
                    'body' => $c->body,
                    'created_at' => $c->created_at->toIso8601String(),
                    'user' => [
                        'id' => $c->user->id,
                        'name' => $c->user->name,
                    ],
                    'can_delete' => $authUser ? $authUser->can('delete', $c) : false,
                    'replies' => $c->replies->map(function (FeedPostComment $r) use ($authUser) {
                        return [
                            'id' => $r->id,
                            'body' => $r->body,
                            'created_at' => $r->created_at->toIso8601String(),
                            'user' => [
                                'id' => $r->user->id,
                                'name' => $r->user->name,
                            ],
                            'can_delete' => $authUser ? $authUser->can('delete', $r) : false,
                        ];
                    })->values()->all(),
                ];
            })->values()->all(),
        ];
    }
}
