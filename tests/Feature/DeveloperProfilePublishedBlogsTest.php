<?php

use App\Enums\BlogStatus;
use App\Models\Developer;
use App\Models\DeveloperBlog;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;

test('public developer profile includes published blogs and excludes drafts', function () {
    $developer = Developer::factory()->create();

    DeveloperBlog::create([
        'developer_id' => $developer->id,
        'title' => 'Published Post',
        'slug' => 'published-post-'.Str::random(8),
        'excerpt' => 'Excerpt text',
        'content' => 'Full content here.',
        'status' => BlogStatus::PUBLISHED,
        'published_at' => now()->subDay(),
    ]);

    DeveloperBlog::create([
        'developer_id' => $developer->id,
        'title' => 'Draft Post',
        'slug' => 'draft-post-'.Str::random(8),
        'excerpt' => null,
        'content' => 'Draft body.',
        'status' => BlogStatus::DRAFT,
        'published_at' => null,
    ]);

    $this->get(route('developers.show', $developer->slug))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Developers/Show')
            ->has('developer.blogs', 1)
            ->where('developer.blogs.0.title', 'Published Post'));
});

test('developer profile lists another developers published blogs when viewer is signed in', function () {
    $profileOwner = Developer::factory()->create();

    DeveloperBlog::create([
        'developer_id' => $profileOwner->id,
        'title' => 'Their Article',
        'slug' => 'their-article-'.Str::random(8),
        'excerpt' => null,
        'content' => 'Content.',
        'status' => BlogStatus::PUBLISHED,
        'published_at' => now()->subHour(),
    ]);

    $viewer = Developer::factory()->create()->user;
    $viewer->markEmailAsVerified();

    $this->actingAs($viewer)
        ->get(route('developers.show', $profileOwner->slug))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Developers/Show')
            ->has('developer.blogs', 1)
            ->where('developer.blogs.0.title', 'Their Article'));
});
