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
                <img 
                    src="{{ $blog->feature_image_url }}" 
                    alt="{{ $blog->title }}" 
                    loading="eager"
                    decoding="async"
                    fetchpriority="high"
                    width="900"
                    height="506"
                    style="aspect-ratio: 16 / 9;"
                >
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
                                    <img 
                                        src="{{ $relatedBlog->feature_image_url }}" 
                                        alt="{{ $relatedBlog->title }}" 
                                        loading="lazy"
                                        decoding="async"
                                        fetchpriority="low"
                                        width="400"
                                        height="180"
                                        style="aspect-ratio: 20 / 9;"
                                    >
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
@endsection
