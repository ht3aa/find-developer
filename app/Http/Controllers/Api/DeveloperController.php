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
     */
    public function index(Request $request)
    {

        $paginator = $this->developerRepository->getPaginated($request, 12);

        return DeveloperResource::collection($paginator);
    }
}
