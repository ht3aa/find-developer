<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeveloperResource;
use App\Models\Developer;
use App\Repositories\DeveloperRepository;
use Illuminate\Http\Request;

class DeveloperController extends Controller
{
    public function __construct(
        private DeveloperRepository $developerRepository
    ) {}

    /**
     * Display a paginated listing of developers with filters.
     * Includes total_developers and recommended_developers counts in the response.
     * Accepts per_page (1–500); default 12 for UI, use 500 for AI/export to get all in one response.
     */
    public function index(Request $request)
    {
        $requested = (int) $request->input('per_page', 12);
        $perPage = min(max(1, $requested), 500);

        $paginator = $this->developerRepository->getPaginated($request, $perPage);

        return DeveloperResource::collection($paginator)->additional([
            'total_developers' => Developer::count(),
            'recommended_developers' => Developer::where('recommended_by_us', true)->count(),
        ]);
    }
}
