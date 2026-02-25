<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeveloperResource;
use App\Models\JobTitle;
use App\Models\Skill;
use App\Repositories\DeveloperRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeveloperController extends Controller
{
    public function __construct(
        private DeveloperRepository $developerRepository
    ) {}

    /**
     * Display a paginated listing of developers with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $paginator = $this->developerRepository->getPaginated($request, 12);

        $jobTitles = JobTitle::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(50)
            ->get(['id', 'name'])
            ->map(fn ($j) => ['value' => $j->name, 'label' => $j->name])
            ->values()
            ->all();

        $skills = Skill::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(50)
            ->get(['id', 'name'])
            ->map(fn ($s) => ['value' => $s->name, 'label' => $s->name])
            ->values()
            ->all();

        return response()->json([
            'developers' => DeveloperResource::collection($paginator->items())->resolve(),
            'job_titles' => $jobTitles,
            'skills' => $skills,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'links' => [
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ]);
    }
}
