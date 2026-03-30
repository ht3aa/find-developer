<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Conversation;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    /**
     * Open (or resume) a Messages thread with the shop contact user for this product order.
     */
    public function contactAdmin(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $adminEmail = config('shop.order_email');
        $admin = User::query()->where('email', $adminEmail)->first();

        if ($admin === null) {
            return redirect()
                ->route('shop.product.show', $product)
                ->with('error', 'Order messaging is temporarily unavailable. Please try again later.');
        }

        $user = $request->user();

        if ($user->id === $admin->id) {
            return redirect()
                ->route('messages.index')
                ->with('info', 'You are the shop contact for this site.');
        }

        [$conversation] = Conversation::findOrCreateBetween($user->id, $admin->id);

        return redirect()
            ->route('messages.show', $conversation)
            ->with(
                'success',
                'Message the admin here with the product you want and attach your payment receipt.',
            );
    }
}
