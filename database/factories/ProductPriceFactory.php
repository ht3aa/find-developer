<?php

namespace Database\Factories;

use App\Enums\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductPrice>
 */
class ProductPriceFactory extends Factory
{
    protected $model = ProductPrice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'price' => fake()->randomFloat(2, 5, 500),
            'currency' => Currency::IQD,
            'is_old_price' => false,
            'is_new_price' => true,
            'is_active' => true,
        ];
    }
}
