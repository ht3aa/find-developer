<div>
    <div class="blogs-container">
        <div class="blogs-header">
            <h1 class="blogs-title">Developer Blogs</h1>
            <p class="blogs-subtitle">Insights, tutorials, and stories from our developer community</p>
        </div>

        <!-- Modern Search Bar -->
        <div class="search-bar-container">
            <div class="search-bar-wrapper">
                <div class="search-bar-row">
                    <div class="search-icon-wrapper">
                        <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by blog title, developer name, or email..."
                        class="search-input"
                    />
                    @if($search)
                        <button 
                            wire:click="$set('search', '')"
                            class="search-clear-btn"
                            type="button"
                            aria-label="Clear search"
                        >
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Loading Indicator -->
        <div wire:loading class="modern-loading">
            <div class="loading-spinner"></div>
            <span class="loading-text">Searching blogs...</span>
        </div>

        <div wire:loading.remove>
        @if($blogs->count() > 0)
            <div class="blogs-grid">
                @foreach($blogs as $blog)
                    <article class="blog-card">
                        @if($blog->featured_image)
                            <div class="blog-image-wrapper">
                                <img src="{{ $blog->feature_image_url }}" alt="{{ $blog->title }}" class="blog-image" loading="lazy">
                            </div>
                        @endif
                        
                        <div class="blog-content">
                            <div class="blog-meta">
                                <span class="blog-author">
                                    <svg class="blog-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ $blog->developer->name }}
                                    @if($blog->developer->jobTitle)
                                        <span class="blog-author-title">• {{ $blog->developer->jobTitle->name }}</span>
                                    @endif
                                </span>
                                <span class="blog-date">
                                    <svg class="blog-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $blog->published_at->format('M d, Y') }}
                                </span>
                            </div>

                        <h2 class="blog-title">
                            <a href="{{ route('blog.show', $blog->slug) }}" class="blog-title-link">
                                {{ $blog->title }}
                            </a>
                        </h2>

                            @if($blog->excerpt)
                                <p class="blog-excerpt">{{ Str::limit($blog->excerpt, 150)  }}...</p>
                            @else
                                <p class="blog-excerpt">{{ Str::limit(strip_tags($blog->content), 150) }}...</p>
                            @endif

                        <div class="blog-footer">
                            <div class="blog-footer-stats">
                                <span class="blog-comments-count">
                                    <svg class="blog-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    {{ $blog->comments_count }} {{ Str::plural('comment', $blog->comments_count) }}
                                </span>
                                <span class="blog-likes-count">
                                    <svg class="blog-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    {{ $blog->likers_count }} {{ Str::plural('like', $blog->likers_count) }}
                                </span>
                            </div>
                            <a href="{{ route('blog.show', $blog->slug) }}" class="blog-read-more">
                                Read More
                                <svg class="blog-arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="blogs-pagination">
                {{ $blogs->links() }}
            </div>
        @else
            <div class="blogs-empty">
                <svg class="blogs-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($search)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    @endif
                </svg>
                <h3 class="blogs-empty-title">
                    {{ $search ? 'No blogs found' : 'No blogs yet' }}
                </h3>
                <p class="blogs-empty-text">
                    {{ $search ? 'Try adjusting your search terms or browse all blogs.' : 'Check back soon for developer insights and stories!' }}
                </p>
            </div>
        @endif
        </div>
    </div>

    <style>
.blogs-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: var(--spacing-xl) var(--spacing-md);
}

.blogs-header {
    text-align: center;
    margin-bottom: var(--spacing-xl);
}

.search-clear-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--spacing-xs);
    background: transparent;
    border: none;
    border-radius: var(--radius-md);
    color: var(--color-gray-400);
    cursor: pointer;
    transition: all var(--transition-base);
    flex-shrink: 0;
}

.search-clear-btn:hover {
    background: var(--bg-tertiary);
    color: var(--text-primary);
}

.search-clear-btn svg {
    width: 1rem;
    height: 1rem;
}

.blogs-title {
    font-size: var(--font-size-4xl);
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: var(--spacing-xs);
}

.blogs-subtitle {
    font-size: var(--font-size-lg);
    color: var(--text-secondary);
    max-width: 600px;
    margin: 0 auto;
}

.blogs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: var(--spacing-xl);
    margin-bottom: var(--spacing-2xl);
}

.blog-card {
    background: var(--bg-primary);
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: transform var(--transition-base), box-shadow var(--transition-base);
    display: flex;
    flex-direction: column;
}

.blog-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.blog-image-wrapper {
    width: 100%;
    height: 200px;
    overflow: hidden;
    background: var(--bg-tertiary);
}

.blog-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.blog-content {
    padding: var(--spacing-lg);
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.blog-meta {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-md);
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
}

.blog-author,
.blog-date,
.blog-comments-count {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.blog-icon {
    width: 16px;
    height: 16px;
}

.blog-author-title {
    color: var(--text-muted);
}

.blog-title {
    margin: 0 0 var(--spacing-sm) 0;
    font-size: var(--font-size-xl);
    font-weight: 600;
    line-height: 1.4;
}

.blog-title-link {
    color: var(--text-primary);
    text-decoration: none;
    transition: color var(--transition-base);
}

.blog-title-link:hover {
    color: var(--color-primary);
}

.blog-excerpt {
    color: var(--text-secondary);
    line-height: 1.6;
    margin: 0 0 var(--spacing-md) 0;
    flex-grow: 1;
}

.blog-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: var(--spacing-md);
    margin-top: auto;
    padding-top: var(--spacing-md);
    border-top: 1px solid var(--border-primary);
}

.blog-footer-stats {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
}

.blog-footer .blog-comments-count,
.blog-footer .blog-likes-count {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.blog-read-more {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    color: var(--color-primary);
    text-decoration: none;
    font-weight: 500;
    font-size: var(--font-size-sm);
    transition: gap var(--transition-base);
}

.blog-read-more:hover {
    gap: var(--spacing-sm);
}

.blog-arrow-icon {
    width: 16px;
    height: 16px;
}

.blogs-pagination {
    display: flex;
    justify-content: center;
    margin-top: var(--spacing-xl);
}

.blogs-empty {
    text-align: center;
    padding: var(--spacing-3xl) var(--spacing-xl);
}

.blogs-empty-icon {
    width: 64px;
    height: 64px;
    color: var(--text-muted);
    margin: 0 auto var(--spacing-md);
}

.blogs-empty-title {
    font-size: var(--font-size-2xl);
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: var(--spacing-xs);
}

.blogs-empty-text {
    color: var(--text-secondary);
}

@media (max-width: 768px) {
    .blogs-title {
        font-size: var(--font-size-3xl);
    }

    .blogs-grid {
        grid-template-columns: 1fr;
        gap: var(--spacing-lg);
    }
}
    </style>
</div>
