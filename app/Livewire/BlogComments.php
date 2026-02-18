<?php

namespace App\Livewire;

use App\Enums\BlogCommentStatus;
use App\Models\BlogComment;
use App\Models\DeveloperBlog;
use App\Models\Scopes\DeveloperScope;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BlogComments extends Component
{
    public string $slug;

    public string $body = '';

    public ?int $replyingToId = null;

    public string $replyBody = '';

    public ?string $message = null;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getBlogProperty(): ?DeveloperBlog
    {
        return DeveloperBlog::with(['developer', 'developer.jobTitle'])
            ->withCount(['comments' => fn ($q) => $q->approved()])
            ->with([
                'rootComments' => fn ($q) => $q->approved()
                    ->orderBy('created_at')
                    ->withCount('likers')
                    ->with([
                        'replies' => fn ($q) => $q->approved()->orderBy('created_at')->withCount('likers'),
                    ]),
            ])
            ->withoutGlobalScopes([DeveloperScope::class])
            ->published()
            ->where('slug', $this->slug)
            ->first();
    }

    public function getLikedCommentIdsProperty(): array
    {
        if (! auth()->check() || ! $this->blog) {
            return [];
        }
        $allCommentIds = $this->blog->rootComments->pluck('id')
            ->merge($this->blog->rootComments->pluck('replies')->flatten()->pluck('id'))
            ->unique()
            ->values();

        return DB::table('blog_comment_likes')
            ->where('user_id', auth()->id())
            ->whereIn('blog_comment_id', $allCommentIds)
            ->pluck('blog_comment_id')
            ->toArray();
    }

    public function storeComment(): void
    {
        if (! auth()->check()) {
            return;
        }
        $this->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $blog = DeveloperBlog::withoutGlobalScopes([DeveloperScope::class])
            ->published()
            ->where('slug', $this->slug)
            ->firstOrFail();

        $user = auth()->user();

        $comment = new BlogComment([
            'name' => $user->name,
            'email' => $user->email,
            'body' => $this->body,
            'status' => BlogCommentStatus::APPROVED,
            'parent_id' => null,
        ]);
        $comment->developer_blog_id = $blog->id;
        $comment->user_id = $user->id;
        $comment->save();

        $this->body = '';
        $this->message = 'Your comment has been added.';
        $this->dispatch('comment-added');
    }

    public function setReplyingTo(int $commentId): void
    {
        $this->replyingToId = $commentId;
        $this->replyBody = '';
        $this->message = null;
    }

    public function cancelReply(): void
    {
        $this->replyingToId = null;
        $this->replyBody = '';
    }

    public function storeReply(int $parentId): void
    {
        if (! auth()->check()) {
            return;
        }
        $this->validate([
            'replyBody' => ['required', 'string', 'max:5000'],
        ]);

        $blog = DeveloperBlog::withoutGlobalScopes([DeveloperScope::class])
            ->published()
            ->where('slug', $this->slug)
            ->firstOrFail();

        $parent = BlogComment::where('developer_blog_id', $blog->id)->approved()->findOrFail($parentId);
        $user = auth()->user();

        $comment = new BlogComment([
            'name' => $user->name,
            'email' => $user->email,
            'body' => $this->replyBody,
            'status' => BlogCommentStatus::APPROVED,
            'parent_id' => $parent->id,
        ]);
        $comment->developer_blog_id = $blog->id;
        $comment->user_id = $user->id;
        $comment->save();

        $this->replyingToId = null;
        $this->replyBody = '';
        $this->message = 'Your reply has been added.';
        $this->dispatch('reply-added');
    }

    public function toggleLike(int $commentId): void
    {
        if (! auth()->check()) {
            return;
        }
        $blog = DeveloperBlog::withoutGlobalScopes([DeveloperScope::class])
            ->published()
            ->where('slug', $this->slug)
            ->firstOrFail();

        $comment = BlogComment::where('id', $commentId)
            ->where('developer_blog_id', $blog->id)
            ->where('status', BlogCommentStatus::APPROVED)
            ->firstOrFail();

        $comment->likers()->toggle([auth()->id()]);
        $this->message = null;
    }

    public function render()
    {
        return view('livewire.blog-comments', [
            'blog' => $this->blog,
            'likedCommentIds' => $this->likedCommentIds,
        ]);
    }
}
