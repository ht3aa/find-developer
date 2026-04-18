<?php

use App\Models\FeedPost;
use App\Models\FeedPostComment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Storage::fake('s3');
});

test('guests can view the feed page', function () {
    $this->get(route('feed.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Feed/Index')
            ->has('posts.data'));
});

test('authenticated user can create a text post', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('feed.store'), [
        'body' => 'Hello from the feed',
    ])->assertRedirect(route('feed.index'));

    $this->assertDatabaseHas('feed_posts', [
        'user_id' => $user->id,
        'body' => 'Hello from the feed',
    ]);
});

test('authenticated user can create a post with images', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $file = UploadedFile::fake()->image('photo.jpg', 400, 300);

    $this->post(route('feed.store'), [
        'body' => 'With image',
        'images' => [$file],
    ])->assertRedirect(route('feed.index'));

    $post = FeedPost::query()->where('user_id', $user->id)->first();
    expect($post)->not->toBeNull();
    expect($post->images)->toHaveCount(1);
    Storage::disk('s3')->assertExists($post->images->first()->path);
});

test('authenticated user can like and unlike a post', function () {
    $author = User::factory()->create();
    $post = FeedPost::factory()->create(['user_id' => $author->id]);
    $liker = User::factory()->create();
    $this->actingAs($liker);

    $this->post(route('feed.like', $post))->assertRedirect();
    expect($post->fresh()->likers)->toHaveCount(1);

    $this->post(route('feed.like', $post))->assertRedirect();
    expect($post->fresh()->likers)->toHaveCount(0);
});

test('authenticated user can comment on a post', function () {
    $author = User::factory()->create();
    $post = FeedPost::factory()->create(['user_id' => $author->id]);
    $commenter = User::factory()->create();
    $this->actingAs($commenter);

    $this->post(route('feed.comments.store', $post), [
        'body' => 'Nice post',
    ])->assertRedirect();

    $this->assertDatabaseHas('feed_post_comments', [
        'feed_post_id' => $post->id,
        'user_id' => $commenter->id,
        'body' => 'Nice post',
        'parent_id' => null,
    ]);
});

test('authenticated user can reply to a root comment only', function () {
    $author = User::factory()->create();
    $post = FeedPost::factory()->create(['user_id' => $author->id]);
    $commenter = User::factory()->create();
    $this->actingAs($commenter);

    $root = FeedPostComment::create([
        'feed_post_id' => $post->id,
        'parent_id' => null,
        'user_id' => $author->id,
        'body' => 'Root',
    ]);

    $this->post(route('feed.comments.store', $post), [
        'body' => 'Reply',
        'parent_id' => $root->id,
    ])->assertRedirect();

    $this->assertDatabaseHas('feed_post_comments', [
        'feed_post_id' => $post->id,
        'user_id' => $commenter->id,
        'parent_id' => $root->id,
        'body' => 'Reply',
    ]);
});

test('replying to non-root comment returns 422', function () {
    $author = User::factory()->create();
    $post = FeedPost::factory()->create(['user_id' => $author->id]);
    $root = FeedPostComment::create([
        'feed_post_id' => $post->id,
        'parent_id' => null,
        'user_id' => $author->id,
        'body' => 'Root',
    ]);
    $reply = FeedPostComment::create([
        'feed_post_id' => $post->id,
        'parent_id' => $root->id,
        'user_id' => $author->id,
        'body' => 'Reply',
    ]);
    $commenter = User::factory()->create();
    $this->actingAs($commenter);

    $this->post(route('feed.comments.store', $post), [
        'body' => 'Nested',
        'parent_id' => $reply->id,
    ])->assertStatus(422);
});

test('user cannot update another users post', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $post = FeedPost::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($other);

    $this->patch(route('feed.update', $post), [
        'body' => 'Hacked',
    ])->assertForbidden();
});

test('feed pagination returns second page', function () {
    $user = User::factory()->create();
    FeedPost::factory()->count(20)->create(['user_id' => $user->id]);

    $this->get(route('feed.index', ['page' => 2]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Feed/Index')
            ->has('posts.data', 5)
            ->where('posts.meta.current_page', 2));
});
