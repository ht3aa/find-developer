<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobTitle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobTitleController extends Controller
{
    /**
     * List job titles, optionally filtered by search.
     */
    public function index(Request $request): JsonResponse
    {

        $query = JobTitle::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(50);

        $search = $request->input('search');
        if ($search && is_string($search)) {
            $term = '%' . addcslashes(trim($search), '%_') . '%';
            $query->where('name', 'like', $term);
        }

        $items = $query->get(['id', 'name'])
            ->map(fn($j) => ['value' => $j->name, 'label' => $j->name])
            ->values()
            ->all();

        return response()->json(['data' => $items]);
    }
}
