<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    /**
     * List badges, optionally filtered by search.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Badge::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(50);

        $search = $request->input('search');
        if ($search && is_string($search)) {
            $term = '%'.addcslashes(trim($search), '%_').'%';
            $query->where('name', 'like', $term);
        }

        $items = $query->get(['id', 'name'])
            ->map(fn ($b) => ['value' => $b->name, 'label' => $b->name])
            ->values()
            ->all();

        return response()->json(['data' => $items]);
    }
}
