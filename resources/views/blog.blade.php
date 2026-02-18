@extends('layouts.app')

@section('title', $blog->title)
@section('seo_title', $blog->title . ' - FindDeveloper')
@section('seo_description', $blog->excerpt ?: Str::limit(strip_tags($blog->content), 160))
@section('seo_keywords', 'developer blog, ' . $blog->developer->name . ', ' . ($blog->developer->jobTitle ? $blog->developer->jobTitle->name : ''))
@if($blog->featured_image)
    @section('seo_image', $blog->feature_image_url)
@endif

@section('content')
<div class="blog-detail-container">
    <article class="blog-detail">
        <!-- Back to Blogs Link -->
        <a href="{{ route('blogs') }}" class="blog-back-link">
            <svg class="blog-back-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Blogs
        </a>

        <!-- Blog Header -->
        <header class="blog-detail-header">
            <h1 class="blog-detail-title">{{ $blog->title }}</h1>
            
            <div class="blog-detail-meta">
                <div class="blog-author-info">
                    <div class="blog-author-avatar">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="blog-author-details">
                        <span class="blog-author-name">{{ $blog->developer->name }}</span>
                        @if($blog->developer->jobTitle)
                            <span class="blog-author-title">{{ $blog->developer->jobTitle->name }}</span>
                        @endif
                    </div>
                </div>
                
                <div class="blog-date-views">
                    <span class="blog-date">
                        <svg class="blog-meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $blog->published_at->format('F d, Y') }}
                    </span>
                    @livewire('blog-like-button', ['slug' => $blog->slug])
                </div>
            </div>
        </header>

        <!-- Featured Image -->
        @if($blog->featured_image)
            <div class="blog-featured-image">
                <img src="{{ $blog->feature_image_url }}" alt="{{ $blog->title }}" loading="lazy">
            </div>
        @endif

        <!-- Blog Content -->
        <div class="blog-detail-content">
            @if($blog->excerpt)
                <div class="blog-excerpt">
                    <p>{{ $blog->excerpt }}</p>
                </div>
            @endif

            <div class="blog-content-body">
                {!! $blog->content !!}
            </div>
        </div>

        <!-- Author Card -->
        <div class="blog-author-card">
            <div class="blog-author-card-avatar">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div class="blog-author-card-info">
                <h3 class="blog-author-card-name">{{ $blog->developer->name }}</h3>
                @if($blog->developer->jobTitle)
                    <p class="blog-author-card-title">{{ $blog->developer->jobTitle->name }}</p>
                @endif
                @if($blog->developer->bio)
                    <p class="blog-author-card-bio">{{ Str::limit($blog->developer->bio, 150) }}</p>
                @endif
                <a href="{{ route('developer.profile', $blog->developer->slug) }}" class="blog-author-card-link">
                    View Profile
                    <svg class="blog-arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </article>

    <!-- Comments (Livewire: updates without redirect) -->
    @livewire('blog-comments', ['slug' => $blog->slug])

    <!-- Related Blogs -->
    @if($relatedBlogs->count() > 0)
        <aside class="blog-related">
            <h2 class="blog-related-title">More from {{ $blog->developer->name }}</h2>
            <div class="blog-related-grid">
                @foreach($relatedBlogs as $relatedBlog)
                    <article class="blog-related-card">
                        @if($relatedBlog->featured_image)
                            <div class="blog-related-image">
                                <a href="{{ route('blog.show', $relatedBlog->slug) }}">
                                    <img src="{{ $relatedBlog->feature_image_url }}" alt="{{ $relatedBlog->title }}" loading="lazy">
                                </a>
                            </div>
                        @endif
                        <div class="blog-related-content">
                            <h3 class="blog-related-card-title">
                                <a href="{{ route('blog.show', $relatedBlog->slug) }}">{{ $relatedBlog->title }}</a>
                            </h3>
                            <div class="blog-related-meta">
                                <span class="blog-related-date">{{ $relatedBlog->published_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </aside>
    @endif
</div>

<style>
.blog-detail-container {
    max-width: 900px;
    margin: 0 auto;
    padding: var(--spacing-xl) var(--spacing-md);
}

.blog-back-link {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    color: var(--text-secondary);
    text-decoration: none;
    margin-bottom: var(--spacing-xl);
    font-size: var(--font-size-sm);
    transition: color var(--transition-base);
}

.blog-back-link:hover {
    color: var(--color-primary);
}

.blog-back-icon {
    width: 16px;
    height: 16px;
}

.blog-detail-header {
    margin-bottom: var(--spacing-xl);
}

.blog-detail-title {
    font-size: var(--font-size-4xl);
    font-weight: 700;
    line-height: 1.2;
    color: var(--text-primary);
    margin-bottom: var(--spacing-lg);
}

.blog-detail-meta {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-lg);
    align-items: center;
    padding-bottom: var(--spacing-lg);
    border-bottom: 1px solid var(--border-primary);
}

.blog-author-info {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.blog-author-avatar {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-full);
    background: var(--bg-tertiary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
}

.blog-author-avatar svg {
    width: 24px;
    height: 24px;
}

.blog-author-details {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
}

.blog-author-name {
    font-weight: 600;
    color: var(--text-primary);
}

.blog-author-title {
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
}

.blog-date-views {
    display: flex;
    gap: var(--spacing-lg);
    align-items: center;
    margin-left: auto;
}

.blog-date {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
}

.blog-meta-icon {
    width: 16px;
    height: 16px;
}

.blog-like-wrapper {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
}

.blog-like-btn {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    padding: var(--spacing-xs) var(--spacing-sm);
    background: none;
    border: 1px solid var(--border-primary);
    border-radius: var(--radius-md);
    color: var(--text-secondary);
    cursor: pointer;
    transition: color var(--transition-base), border-color var(--transition-base), background var(--transition-base);
}

.blog-like-btn:hover {
    color: var(--color-primary);
    border-color: var(--color-primary);
}

.blog-like-btn--liked {
    color: var(--color-primary);
    border-color: var(--color-primary);
}

.blog-like-icon {
    width: 18px;
    height: 18px;
}

.blog-like-count {
    font-weight: 500;
}

.blog-like-wrapper--guest {
    padding: var(--spacing-xs) var(--spacing-sm);
    border: 1px solid var(--border-primary);
    border-radius: var(--radius-md);
}

.blog-like-login-link {
    margin-left: var(--spacing-xs);
    color: var(--color-primary);
    font-size: var(--font-size-sm);
    text-decoration: none;
}

.blog-like-login-link:hover {
    text-decoration: underline;
}

.blog-like-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.blog-like-spinner {
    display: inline-block;
    width: 18px;
    height: 18px;
    border: 2px solid var(--border-primary);
    border-top-color: var(--color-primary);
    border-radius: 50%;
    animation: blog-like-spin 0.6s linear infinite;
}

@keyframes blog-like-spin {
    to { transform: rotate(360deg); }
}

.blog-featured-image {
    width: 100%;
    margin-bottom: var(--spacing-xl);
    border-radius: var(--radius-xl);
    overflow: hidden;
}

.blog-featured-image img {
    width: 100%;
    height: auto;
    display: block;
}

.blog-detail-content {
    margin-bottom: var(--spacing-2xl);
}

.blog-excerpt {
    font-size: var(--font-size-xl);
    line-height: 1.6;
    color: var(--text-primary);
    margin-bottom: var(--spacing-xl);
    padding: var(--spacing-lg);
    background: var(--bg-secondary);
    border-left: 4px solid var(--color-primary);
    border-radius: var(--radius-sm);
}

.blog-content-body {
    font-size: var(--font-size-lg);
    line-height: 1.8;
    color: var(--text-primary);
}

.blog-content-body h1,
.blog-content-body h2,
.blog-content-body h3 {
    margin-top: var(--spacing-xl);
    margin-bottom: var(--spacing-md);
    font-weight: 600;
    color: var(--text-primary);
}

.blog-content-body h2 {
    font-size: var(--font-size-3xl);
}

.blog-content-body h3 {
    font-size: var(--font-size-2xl);
}

.blog-content-body p {
    margin-bottom: var(--spacing-lg);
}

.blog-content-body ul,
.blog-content-body ol {
    margin-bottom: var(--spacing-lg);
    padding-left: var(--spacing-xl);
}

.blog-content-body li {
    margin-bottom: var(--spacing-xs);
}

.blog-content-body a {
    color: var(--color-primary);
    text-decoration: underline;
}

.blog-content-body a:hover {
    color: var(--color-primary-dark);
}

.blog-content-body blockquote {
    border-left: 4px solid var(--color-primary);
    padding-left: var(--spacing-lg);
    margin: var(--spacing-lg) 0;
    font-style: italic;
    color: var(--text-secondary);
}

.blog-content-body code {
    background: var(--bg-tertiary);
    padding: var(--spacing-xs) var(--spacing-xs);
    border-radius: var(--radius-sm);
    font-size: 0.9em;
}

.blog-content-body pre {
    background: var(--color-gray-900);
    color: var(--color-gray-50);
    padding: var(--spacing-lg);
    border-radius: var(--radius-lg);
    overflow-x: auto;
    margin-bottom: var(--spacing-lg);
}

.blog-content-body pre code {
    background: transparent;
    padding: 0;
    color: inherit;
}

.blog-author-card {
    display: flex;
    gap: var(--spacing-lg);
    padding: var(--spacing-xl);
    background: var(--bg-secondary);
    border-radius: var(--radius-xl);
    margin-bottom: var(--spacing-2xl);
}

.blog-author-card-avatar {
    width: 64px;
    height: 64px;
    border-radius: var(--radius-full);
    background: var(--bg-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
    flex-shrink: 0;
}

.blog-author-card-avatar svg {
    width: 32px;
    height: 32px;
}

.blog-author-card-info {
    flex-grow: 1;
}

.blog-author-card-name {
    font-size: var(--font-size-xl);
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: var(--spacing-xs);
}

.blog-author-card-title {
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
    margin-bottom: var(--spacing-sm);
}

.blog-author-card-bio {
    color: var(--text-secondary);
    line-height: 1.6;
    margin-bottom: var(--spacing-md);
}

.blog-author-card-link {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    color: var(--color-primary);
    text-decoration: none;
    font-weight: 500;
    transition: gap var(--transition-base);
}

.blog-author-card-link:hover {
    gap: var(--spacing-sm);
}

.blog-arrow-icon {
    width: 16px;
    height: 16px;
}

.blog-related {
    margin-top: var(--spacing-3xl);
    padding-top: var(--spacing-2xl);
    border-top: 1px solid var(--border-primary);
}

.blog-related-title {
    font-size: var(--font-size-2xl);
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: var(--spacing-lg);
}

.blog-related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: var(--spacing-lg);
}

.blog-related-card {
    background: var(--bg-primary);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: transform var(--transition-base), box-shadow var(--transition-base);
}

.blog-related-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.blog-related-image {
    width: 100%;
    height: 180px;
    overflow: hidden;
    background: var(--bg-tertiary);
}

.blog-related-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.blog-related-content {
    padding: var(--spacing-md);
}

.blog-related-card-title {
    font-size: var(--font-size-base);
    font-weight: 600;
    margin-bottom: var(--spacing-sm);
    line-height: 1.4;
}

.blog-related-card-title a {
    color: var(--text-primary);
    text-decoration: none;
    transition: color var(--transition-base);
}

.blog-related-card-title a:hover {
    color: var(--color-primary);
}

.blog-related-meta {
    display: flex;
    gap: var(--spacing-md);
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
}

/* Comments */
.blog-comments {
    margin-top: var(--spacing-2xl);
    padding-top: var(--spacing-2xl);
    border-top: 1px solid var(--border-primary);
}

.blog-comments-title {
    font-size: var(--font-size-2xl);
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: var(--spacing-lg);
}

.blog-comments-count {
    font-weight: 400;
    color: var(--text-secondary);
}

.blog-comment-success {
    padding: var(--spacing-md);
    margin-bottom: var(--spacing-lg);
    background: rgba(34, 197, 94, 0.1);
    border: 1px solid rgba(34, 197, 94, 0.3);
    border-radius: var(--radius-md);
    color: var(--text-primary);
}

.blog-comments-list {
    list-style: none;
    padding: 0;
    margin: 0 0 var(--spacing-2xl);
}

.blog-comment {
    display: flex;
    gap: var(--spacing-md);
    padding: var(--spacing-lg) 0;
    border-bottom: 1px solid var(--border-primary);
}

.blog-comment:last-child {
    border-bottom: none;
}

.blog-comment-avatar {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-full);
    background: var(--bg-tertiary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
    flex-shrink: 0;
}

.blog-comment-avatar svg {
    width: 20px;
    height: 20px;
}

.blog-comment-body {
    flex-grow: 1;
    min-width: 0;
}

.blog-comment-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-xs);
}

.blog-comment-author {
    font-weight: 600;
    color: var(--text-primary);
}

.blog-comment-date {
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
}

.blog-comment-text {
    font-size: var(--font-size-base);
    line-height: 1.6;
    color: var(--text-primary);
    margin: 0;
    white-space: pre-wrap;
}

.blog-comment-actions {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    margin-top: var(--spacing-sm);
}

.blog-comment-like-form {
    display: inline;
    margin: 0;
}

.blog-comment-action-btn {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    padding: 0;
    background: none;
    border: none;
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
    cursor: pointer;
    transition: color var(--transition-base);
}

.blog-comment-action-btn:hover {
    color: var(--color-primary);
}

.blog-comment-action-icon {
    width: 18px;
    height: 18px;
}

.blog-comment-like-btn.is-liked {
    color: var(--color-primary);
}

.blog-comment-like-count {
    font-size: var(--font-size-sm);
}

.blog-comment--reply {
    padding-left: var(--spacing-lg);
    border-left: 2px solid var(--border-primary);
}

.blog-comments-list--replies {
    margin-top: var(--spacing-md);
    margin-bottom: 0;
    padding-left: 0;
}

.blog-comment-reply-form-wrapper {
    margin-top: var(--spacing-md);
}

.blog-comment-reply-form {
    gap: var(--spacing-sm);
}

.blog-comment-reply-form textarea {
    padding: var(--spacing-sm) var(--spacing-md);
    border: 1px solid var(--border-primary);
    border-radius: var(--radius-md);
    background: var(--bg-primary);
    color: var(--text-primary);
    font-size: var(--font-size-base);
    font-family: inherit;
    width: 100%;
    resize: vertical;
    min-height: 80px;
}

.blog-comment-reply-form-actions {
    display: flex;
    gap: var(--spacing-sm);
    align-items: center;
}

.blog-comment-submit--sm {
    padding: var(--spacing-xs) var(--spacing-md);
    font-size: var(--font-size-sm);
}

.blog-comment-cancel-reply {
    padding: var(--spacing-xs) var(--spacing-md);
    background: none;
    border: none;
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
    cursor: pointer;
}

.blog-comment-cancel-reply:hover {
    color: var(--text-primary);
}

.blog-comments-empty {
    color: var(--text-secondary);
    margin-bottom: var(--spacing-2xl);
}

.blog-comment-form-wrapper {
    background: var(--bg-secondary);
    padding: var(--spacing-xl);
    border-radius: var(--radius-xl);
}

.blog-comment-form-title {
    font-size: var(--font-size-lg);
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: var(--spacing-lg);
}

.blog-comment-form {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
}

.blog-comment-form-group {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
}

.blog-comment-form-group label {
    font-size: var(--font-size-sm);
    font-weight: 500;
    color: var(--text-primary);
}

.blog-comment-form-group .required {
    color: var(--color-primary);
}

.blog-comment-form-group input,
.blog-comment-form-group textarea {
    padding: var(--spacing-sm) var(--spacing-md);
    border: 1px solid var(--border-primary);
    border-radius: var(--radius-md);
    background: var(--bg-primary);
    color: var(--text-primary);
    font-size: var(--font-size-base);
    font-family: inherit;
}

.blog-comment-form-group input:focus,
.blog-comment-form-group textarea:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 2px rgba(var(--color-primary-rgb, 59, 130, 246), 0.2);
}

.blog-comment-form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.blog-comment-error {
    font-size: var(--font-size-sm);
    color: #dc2626;
}

.blog-comment-submit {
    align-self: flex-start;
    padding: var(--spacing-sm) var(--spacing-lg);
    background: var(--color-primary);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-weight: 500;
    font-size: var(--font-size-base);
    cursor: pointer;
    transition: opacity var(--transition-base);
}

.blog-comment-submit:hover {
    opacity: 0.9;
}

.blog-comment-login-prompt {
    color: var(--text-secondary);
    margin: 0;
}

.blog-comment-login-prompt a {
    color: var(--color-primary);
    text-decoration: none;
}

.blog-comment-login-prompt a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .blog-detail-title {
        font-size: var(--font-size-3xl);
    }

    .blog-detail-meta {
        flex-direction: column;
        align-items: flex-start;
    }

    .blog-date-views {
        margin-left: 0;
    }

    .blog-author-card {
        flex-direction: column;
        text-align: center;
    }

    .blog-related-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
