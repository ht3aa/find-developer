<section class="blog-comments" wire:key="blog-comments-{{ $blog?->id }}">
    @if(!$blog)
        <p class="blog-comments-empty">Blog not found.</p>
    @else

    <h2 class="blog-comments-title">
        Comments
        @if($blog->comments_count > 0)
            <span class="blog-comments-count">({{ $blog->comments_count }})</span>
        @endif
    </h2>

    @if($message)
        <p class="blog-comment-success">{{ $message }}</p>
    @endif

    @if($blog->rootComments->count() > 0)
        <ul class="blog-comments-list">
            @foreach($blog->rootComments as $comment)
                @include('livewire.blog-comment-item', [
                    'comment' => $comment,
                    'blog' => $blog,
                    'likedCommentIds' => $likedCommentIds,
                    'depth' => 0,
                    'replyingToId' => $replyingToId ?? null,
                ])
            @endforeach
        </ul>
    @else
        <p class="blog-comments-empty">No comments yet. Be the first to share your thoughts!</p>
    @endif

    <div class="blog-comment-form-wrapper">
        <h3 class="blog-comment-form-title">Leave a comment</h3>
        @auth
            <form wire:submit.prevent="storeComment" class="blog-comment-form">
                <div class="blog-comment-form-group">
                    <label for="comment-body">Comment <span class="required">*</span></label>
                    <textarea
                        id="comment-body"
                        wire:model.blur="body"
                        rows="4"
                        maxlength="5000"
                        placeholder="Share your thoughts..."
                        required
                    ></textarea>
                    @error('body')
                        <span class="blog-comment-error">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="blog-comment-submit">Post comment</button>
            </form>
        @else
            <p class="blog-comment-login-prompt">
                <a href="{{ route('filament.admin.auth.login') }}">Log in</a> to leave a comment.
            </p>
        @endauth
    </div>
    @endif
</section>
