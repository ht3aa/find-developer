<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    /**
     * Display the shop catalog (active products only).
     */
    public function index(): Response
    {
        $paginator = Product::query()
            ->where('is_active', true)
            ->with([
                'category',
                'images' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderByDesc('is_featured'),
                'prices' => fn ($query) => $query->where('is_active', true),
            ])
            ->orderBy('name')
            ->paginate(24);

        $payload = ProductResource::collection($paginator)
            ->response()
            ->getData(true);

        return Inertia::render('Shop', [
            'products' => $payload,
        ]);
    }

    /**
     * Display a single active product (by slug).
     */
    public function show(Product $product): Response
    {
        abort_unless($product->is_active, 404);

        $product->load([
            'category',
            'images' => fn ($query) => $query
                ->where('is_active', true)
                ->orderByDesc('is_featured'),
            'prices' => fn ($query) => $query->where('is_active', true),
        ]);

        return Inertia::render('Shop/ProductShow', [
            'product' => ProductResource::make($product)->resolve(),
            'orderEmail' => config('shop.order_email'),
        ]);
    }
}
