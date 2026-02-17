<?php

namespace App\Http\Controllers;

use App\Models\DeveloperBlog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function show($slug)
    {
        $blog = DeveloperBlog::with(['developer', 'developer.jobTitle'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Get related blogs (same developer, excluding current blog)
        $relatedBlogs = DeveloperBlog::with(['developer', 'developer.jobTitle'])
            ->published()
            ->where('developer_id', $blog->developer_id)
            ->where('id', '!=', $blog->id)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return view('blog', compact('blog', 'relatedBlogs'));
    }
}
