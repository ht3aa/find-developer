<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ProductRepository
{
    /**
     * Paginated products with optional filters, sorts, and includes.
     */
    public function getPaginated(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(Product::class)
            ->allowedFilters(
                AllowedFilter::exact('category_id'),
                AllowedFilter::exact('slug'),
                AllowedFilter::exact('is_active'),
                AllowedFilter::callback('search', function ($query, mixed $value): void {
                    if (blank($value)) {
                        return;
                    }

                    if (is_array($value)) {
                        $value = implode(' ', $value);
                    }

                    $term = '%'.addcslashes((string) $value, '%_').'%';
                    $query->where(function ($query) use ($term): void {
                        $query->where('products.name', 'like', $term)
                            ->orWhere('products.description', 'like', $term);
                    });
                }),
            )
            ->allowedIncludes('category', 'images', 'prices')
            ->allowedSorts(
                AllowedSort::field('name'),
                AllowedSort::field('created_at'),
                AllowedSort::field('id'),
            )
            ->defaultSort('name')
            ->paginate($perPage)
            ->appends($request->query());
    }
}
