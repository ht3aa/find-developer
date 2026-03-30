<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;

test('products index returns paginated json', function () {
    Category::factory()->create();
    Product::factory()->count(2)->create();

    $response = $this->getJson(route('api.products.index'));

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'name',
                'slug',
                'description',
                'category_id',
                'is_active',
                'created_at',
                'updated_at',
            ],
        ],
        'links',
        'meta',
    ]);
});

test('products index can filter by category_id', function () {
    $categoryA = Category::factory()->create();
    $categoryB = Category::factory()->create();
    $productA = Product::factory()->for($categoryA)->create();
    Product::factory()->for($categoryB)->create();

    $response = $this->getJson('/api/products?filter[category_id]='.$categoryA->id);

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['id'])->toBe($productA->id);
});

test('products index can include category relationship', function () {
    $category = Category::factory()->create(['name' => 'Electronics']);
    Product::factory()->for($category)->create();

    $response = $this->getJson('/api/products?include=category');

    $response->assertOk();
    expect($response->json('data.0.category.name'))->toBe('Electronics');
});

test('products index include images only serializes active images', function () {
    $product = Product::factory()->create();
    $activeA = ProductImage::factory()->for($product)->create(['is_active' => true]);
    $activeB = ProductImage::factory()->for($product)->create(['is_active' => true]);
    $inactive = ProductImage::factory()->inactive()->for($product)->create();

    $response = $this->getJson('/api/products?include=images&filter[slug]='.$product->slug);

    $response->assertOk();
    $images = $response->json('data.0.images');
    expect($images)->toHaveCount(2);
    expect(collect($images)->pluck('id')->all())->toContain($activeA->id, $activeB->id);
    expect(collect($images)->pluck('id')->all())->not->toContain($inactive->id);
});
