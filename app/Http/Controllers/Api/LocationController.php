<?php

namespace App\Http\Controllers\Api;

use App\Enums\WorldGovernorate;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * List world governorates (developer location enum), optionally filtered by search.
     *
     * @return JsonResponse{data: list<array{value: string, label: string}>}
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $term = is_string($search) ? mb_strtolower(trim($search)) : '';

        $items = collect(WorldGovernorate::cases())
            ->map(fn (WorldGovernorate $g) => [
                'value' => $g->value,
                'label' => $g->getLabel(),
            ])
            ->when($term !== '', function ($collection) use ($term) {
                return $collection->filter(function (array $item) use ($term) {
                    return str_contains(mb_strtolower($item['label']), $term)
                        || str_contains(mb_strtolower($item['value']), $term);
                });
            })
            ->sortBy('label', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->take(50)
            ->all();

        return response()->json(['data' => $items]);
    }
}
