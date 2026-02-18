@php
    $isLiked = in_array($comment->id, $likedCommentIds ?? []);
    $likersCount = $comment->likers_count ?? 0;
    $isReplying = isset($replyingToId) && $replyingToId === $comment->id;
@endphp
<li class="blog-comment {{ $depth > 0 ? 'blog-comment--reply' : '' }}" wire:key="comment-{{ $comment->id }}">
    <div class="blog-comment-avatar">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
    </div>
    <div class="blog-comment-body">
        <div class="blog-comment-meta">
            <span class="blog-comment-author">{{ $comment->name }}</span>
            <span class="blog-comment-date">{{ $comment->created_at->format('M d, Y \a\t g:i A') }}</span>
        </div>
        <p class="blog-comment-text">{{ $comment->body }}</p>
        <div class="blog-comment-actions">
            @auth
                <button
                    type="button"
                    wire:click="toggleLike({{ $comment->id }})"
                    class="blog-comment-action-btn blog-comment-like-btn {{ $isLiked ? 'is-liked' : '' }}"
                    aria-label="{{ $isLiked ? 'Unlike' : 'Like' }}"
                >
                    <svg class="blog-comment-action-icon" fill="{{ $isLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    @if($likersCount > 0)
                        <span class="blog-comment-like-count">{{ $likersCount }}</span>
                    @endif
                </button>
                <button
                    type="button"
                    wire:click="setReplyingTo({{ $comment->id }})"
                    class="blog-comment-action-btn blog-comment-reply-btn"
                    aria-label="Reply"
                >Reply</button>
            @endauth
        </div>
        @auth
            @if($isReplying)
                <div class="blog-comment-reply-form-wrapper">
                    <form wire:submit.prevent="storeReply({{ $comment->id }})" class="blog-comment-form blog-comment-reply-form">
                        <div class="blog-comment-form-group">
                            <textarea
                                wire:model.blur="replyBody"
                                rows="3"
                                maxlength="5000"
                                placeholder="Write a reply..."
                                required
                            ></textarea>
                            @error('replyBody')
                                <span class="blog-comment-error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="blog-comment-reply-form-actions">
                            <button type="submit" class="blog-comment-submit blog-comment-submit--sm">Reply</button>
                            <button type="button" wire:click="cancelReply" class="blog-comment-cancel-reply">Cancel</button>
                        </div>
                    </form>
                </div>
            @endif
        @endauth
        @if($comment->replies->count() > 0)
            <ul class="blog-comments-list blog-comments-list--replies">
                @foreach($comment->replies as $reply)
                    @include('livewire.blog-comment-item', [
                        'comment' => $reply,
                        'blog' => $blog,
                        'likedCommentIds' => $likedCommentIds ?? [],
                        'depth' => $depth + 1,
                        'replyingToId' => $replyingToId ?? null,
                    ])
                @endforeach
            </ul>
        @endif
    </div>
</li>
