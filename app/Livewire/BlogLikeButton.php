<?php

namespace App\Livewire;

use App\Models\DeveloperBlog;
use App\Models\Scopes\DeveloperScope;
use Livewire\Component;

class BlogLikeButton extends Component
{
    public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getBlogProperty(): ?DeveloperBlog
    {
        return DeveloperBlog::withCount('likers')
            ->withoutGlobalScopes([DeveloperScope::class])
            ->published()
            ->where('slug', $this->slug)
            ->first();
    }

    public function getUserLikedProperty(): bool
    {
        if (! auth()->check() || ! $this->blog) {
            return false;
        }

        return $this->blog->likers()->where('users.id', auth()->id())->exists();
    }

    public function toggleLike(): void
    {
        if (! auth()->check()) {
            return;
        }

        $blog = DeveloperBlog::withoutGlobalScopes([DeveloperScope::class])
            ->published()
            ->where('slug', $this->slug)
            ->firstOrFail();

        $blog->likers()->toggle([auth()->id()]);
    }

    public function render()
    {
        $blog = $this->blog;
        $userLiked = $this->userLiked;

        return view('livewire.blog-like-button', [
            'blog' => $blog,
            'userLiked' => $userLiked,
        ]);
    }
}
