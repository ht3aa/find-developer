<?php

use App\Models\Conversation;
use App\Models\Product;
use App\Models\User;

test('guest is redirected to login when visiting shop contact admin', function () {
    $product = Product::factory()->create([
        'is_active' => true,
    ]);

    $this->get(route('shop.product.contact-admin', $product))
        ->assertRedirect(route('login'));
});

test('authenticated user is redirected to conversation with shop admin', function () {
    $admin = User::factory()->create([
        'email' => 'ht3aa2001@gmail.com',
    ]);
    $buyer = User::factory()->create();
    $product = Product::factory()->create([
        'is_active' => true,
    ]);

    $response = $this->actingAs($buyer)->get(route('shop.product.contact-admin', $product));

    $conversation = Conversation::query()
        ->whereHas('participants', fn ($q) => $q->where('user_id', $buyer->id))
        ->whereHas('participants', fn ($q) => $q->where('user_id', $admin->id))
        ->firstOrFail();

    $response->assertRedirect(route('messages.show', $conversation));
});

test('shop contact admin returns 404 for inactive product', function () {
    $product = Product::factory()->inactive()->create();
    $buyer = User::factory()->create();

    $this->actingAs($buyer)->get(route('shop.product.contact-admin', $product))
        ->assertNotFound();
});

test('shop contact admin redirects to product page when admin user is missing', function () {
    $buyer = User::factory()->create();
    $product = Product::factory()->create([
        'is_active' => true,
    ]);

    $this->actingAs($buyer)->get(route('shop.product.contact-admin', $product))
        ->assertRedirect(route('shop.product.show', $product))
        ->assertSessionHas('error');
});

test('shop admin is redirected to messages index when opening contact admin', function () {
    $admin = User::factory()->create([
        'email' => 'ht3aa2001@gmail.com',
    ]);
    $product = Product::factory()->create([
        'is_active' => true,
    ]);

    $this->actingAs($admin)->get(route('shop.product.contact-admin', $product))
        ->assertRedirect(route('messages.index'))
        ->assertSessionHas('info');
});
