<?php

namespace App\Http\Controllers;

use App\Models\DeveloperBlog;
use App\Models\Scopes\ApprovedScope;
use App\Models\Scopes\DeveloperScope;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicBlogController extends Controller
{
    /**
     * Display a listing of published blog posts (public).
     */
    public function index(Request $request): Response
    {
        $blogs = DeveloperBlog::withoutGlobalScope(DeveloperScope::class)
            ->published()
            ->with(['developer' => fn ($q) => $q->withoutGlobalScope(ApprovedScope::class)->select('id', 'name', 'slug')])
            ->orderByDesc('published_at')
            ->paginate(12)
            ->withQueryString()
            ->through(fn (DeveloperBlog $b) => [
                'id' => $b->id,
                'title' => $b->title,
                'slug' => $b->slug,
                'excerpt' => $b->excerpt,
                'published_at' => $b->published_at?->toIso8601String(),
                'featured_image_url' => $b->feature_image_url,
                'developer' => $b->developer ? [
                    'name' => $b->developer->name,
                    'slug' => $b->developer->slug,
                ] : null,
            ]);

        return Inertia::render('Blog/Index', [
            'blogs' => $blogs,
        ]);
    }

    /**
     * Display the specified published blog post (public).
     */
    public function show(Request $request, string $slug): Response
    {
        $blog = DeveloperBlog::withoutGlobalScope(DeveloperScope::class)
            ->published()
            ->where('slug', $slug)
            ->with(['developer' => fn ($q) => $q->withoutGlobalScope(ApprovedScope::class)->select('id', 'name', 'slug')])
            ->firstOrFail();

        return Inertia::render('Blog/Show', [
            'blog' => [
                'id' => $blog->id,
                'title' => $blog->title,
                'slug' => $blog->slug,
                'excerpt' => $blog->excerpt,
                'content' => $blog->content,
                'published_at' => $blog->published_at?->toIso8601String(),
                'featured_image_url' => $blog->feature_image_url,
                'developer' => $blog->developer ? [
                    'name' => $blog->developer->name,
                    'slug' => $blog->developer->slug,
                ] : null,
            ],
        ]);
    }
}
