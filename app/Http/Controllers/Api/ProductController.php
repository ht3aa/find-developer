<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductRepository $productRepository
    ) {}

    /**
     * Display a paginated listing of products with filters, sorts, and includes.
     *
     * Accepts per_page (1–500); default 15.
     */
    public function index(Request $request)
    {
        $requested = (int) $request->input('per_page', 15);
        $perPage = min(max(1, $requested), 500);

        $paginator = $this->productRepository->getPaginated($request, $perPage);

        return ProductResource::collection($paginator);
    }
}
