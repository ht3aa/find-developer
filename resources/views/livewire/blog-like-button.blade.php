<div class="blog-like-wrapper" wire:key="blog-like-{{ $blog?->id ?? 'none' }}">
    @if($blog)
        @auth
            <button
                type="button"
                wire:click="toggleLike"
                wire:loading.attr="disabled"
                wire:target="toggleLike"
                class="blog-like-btn {{ $userLiked ? 'blog-like-btn--liked' : '' }}"
                aria-label="{{ $userLiked ? 'Unlike this post' : 'Like this post' }}"
            >
                <span wire:loading.remove wire:target="toggleLike">
                    <svg class="blog-like-icon" fill="{{ $userLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </span>
                <span wire:loading wire:target="toggleLike" class="blog-like-spinner"></span>
                <span class="blog-like-count">{{ $blog->likers_count }}</span>
                <span class="blog-like-label">{{ $blog->likers_count === 1 ? 'Like' : 'Likes' }}</span>
            </button>
        @else
            <div class="blog-like-wrapper blog-like-wrapper--guest">
                <svg class="blog-like-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span class="blog-like-count">{{ $blog->likers_count }}</span>
                <span class="blog-like-label">{{ $blog->likers_count === 1 ? 'Like' : 'Likes' }}</span>
                <a href="{{ route('filament.admin.auth.login') }}" class="blog-like-login-link">Log in to like</a>
            </div>
        @endauth
    @endif
</div>
