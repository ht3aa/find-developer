<?php

use App\Enums\Currency;
use App\Models\ProductPrice;

test('product price factory defaults currency to IQD', function () {
    $price = ProductPrice::factory()->create();

    expect($price->currency)->toBe(Currency::IQD);
});

test('product price can persist USD', function () {
    $price = ProductPrice::factory()->create([
        'currency' => Currency::USD,
    ]);

    expect($price->fresh()->currency)->toBe(Currency::USD);
});
