<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreDeveloperBlogRequest;
use App\Http\Requests\Dashboard\UpdateDeveloperBlogRequest;
use App\Models\DeveloperBlog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeveloperBlogController extends Controller
{
    /**
     * Display the developer blogs listing for the authenticated user.
     */
    public function index(Request $request): Response|RedirectResponse
    {
        $this->authorize('viewAny', DeveloperBlog::class);

        $developer = $request->user()->developer;

        if (! $developer) {
            return redirect()->route('dashboard.developer-profile.index')
                ->withErrors(['developer' => 'You must have a developer profile to manage blogs.']);
        }

        $blogs = DeveloperBlog::query()
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (DeveloperBlog $b) => [
                'id' => $b->id,
                'title' => $b->title,
                'slug' => $b->slug,
                'excerpt' => $b->excerpt,
                'status' => $b->status->value,
                'status_label' => $b->status->getLabel(),
                'published_at' => $b->published_at?->format('Y-m-d H:i'),
                'created_at' => $b->created_at->toIso8601String(),
            ]);

        $user = $request->user();

        return Inertia::render('DeveloperBlog/Index', [
            'blogs' => $blogs,
            'can' => [
                'updateDeveloperBlog' => $user->can('update', new DeveloperBlog),
                'deleteDeveloperBlog' => $user->can('delete', new DeveloperBlog),
                'changeStatusDeveloperBlog' => $user->can('changeStatus', new DeveloperBlog),
            ],
        ]);
    }

    /**
     * Show the form for creating a new developer blog.
     */
    public function create(Request $request): Response|RedirectResponse
    {
        $this->authorize('create', DeveloperBlog::class);

        $developer = $request->user()->developer;

        if (! $developer) {
            return redirect()->route('dashboard.developer-profile.index')
                ->withErrors(['developer' => 'You must have a developer profile to add blogs.']);
        }

        $user = $request->user();

        return Inertia::render('DeveloperBlog/Create', [
            'statusOptions' => array_map(
                fn ($case) => ['value' => $case->value, 'label' => $case->getLabel()],
                \App\Enums\BlogStatus::cases()
            ),
            'canChangeStatus' => $user->isSuperAdmin(),
        ]);
    }

    /**
     * Store a newly created developer blog.
     */
    public function store(StoreDeveloperBlogRequest $request): RedirectResponse
    {
        $this->authorize('create', DeveloperBlog::class);

        $developer = $request->user()->developer;

        if (! $developer) {
            return redirect()->route('dashboard.developer-profile.index')
                ->withErrors(['developer' => 'You must have a developer profile to add blogs.']);
        }

        $data = $request->validated();
        $data['developer_id'] = $developer->id;

        if (! $request->user()->isSuperAdmin()) {
            $data['status'] = \App\Enums\BlogStatus::DRAFT;
            $data['published_at'] = null;
        } elseif (empty($data['published_at'] ?? null) && ($data['status'] ?? '') === 'published') {
            $data['published_at'] = now();
        }

        DeveloperBlog::create($data);

        return redirect()
            ->route('developer-blogs.index')
            ->with('success', 'Blog post created successfully.');
    }

    /**
     * Show the form for editing the specified developer blog.
     */
    public function edit(Request $request, DeveloperBlog $developer_blog): Response|RedirectResponse
    {
        $this->authorize('update', $developer_blog);

        $user = $request->user();

        return Inertia::render('DeveloperBlog/Edit', [
            'blog' => [
                'id' => $developer_blog->id,
                'title' => $developer_blog->title,
                'slug' => $developer_blog->slug,
                'excerpt' => $developer_blog->excerpt,
                'content' => $developer_blog->content,
                'featured_image' => $developer_blog->featured_image,
                'status' => $developer_blog->status->value,
                'published_at' => $developer_blog->published_at?->format('Y-m-d\TH:i'),
            ],
            'statusOptions' => array_map(
                fn ($case) => ['value' => $case->value, 'label' => $case->getLabel()],
                \App\Enums\BlogStatus::cases()
            ),
            'canChangeStatus' => $user->isSuperAdmin(),
        ]);
    }

    /**
     * Update the specified developer blog.
     */
    public function update(UpdateDeveloperBlogRequest $request, DeveloperBlog $developer_blog): RedirectResponse
    {
        $this->authorize('update', $developer_blog);

        $data = $request->validated();

        if (! $request->user()->isSuperAdmin()) {
            unset($data['status'], $data['published_at']);
        } else {
            if (empty($data['published_at'] ?? null) && ($data['status'] ?? '') === 'published') {
                $data['published_at'] = $developer_blog->published_at ?? now();
            }
            if (($data['status'] ?? '') !== 'published') {
                $data['published_at'] = null;
            }
        }

        $developer_blog->update($data);

        return redirect()
            ->route('developer-blogs.index')
            ->with('success', 'Blog post updated successfully.');
    }

    /**
     * Remove the specified developer blog.
     */
    public function destroy(Request $request, DeveloperBlog $developer_blog): RedirectResponse
    {
        $this->authorize('delete', $developer_blog);

        $developer_blog->delete();

        return redirect()
            ->route('developer-blogs.index')
            ->with('success', 'Blog post deleted successfully.');
    }
}
