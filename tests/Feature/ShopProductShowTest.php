<?php

use App\Models\Product;
use Inertia\Testing\AssertableInertia as Assert;

test('shop product page renders for active product', function () {
    $product = Product::factory()->create([
        'is_active' => true,
        'slug' => 'cool-widget',
    ]);

    $response = $this->get(route('shop.product.show', $product));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Shop/ProductShow')
        ->where('product.slug', 'cool-widget')
        ->where('orderEmail', 'ht3aa2001@gmail.com'));
});

test('shop product page returns 404 for inactive product', function () {
    $product = Product::factory()->inactive()->create();

    $this->get(route('shop.product.show', $product))->assertNotFound();
});
