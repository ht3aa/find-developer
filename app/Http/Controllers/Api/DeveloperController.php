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
     */
    public function index(Request $request)
    {
        $paginator = $this->developerRepository->getPaginated($request, 12);

        return DeveloperResource::collection($paginator)->additional([
            'total_developers' => $paginator->total(),
            'recommended_developers' => Developer::where('recommended_by_us', true)->count(),
        ]);
    }
}
