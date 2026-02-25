<?php

namespace App\Http\Controllers;

use App\Enums\RecommendationStatus;
use App\Http\Resources\DeveloperResource;
use App\Models\Developer;
use App\Models\Scopes\DeveloperScope;
use App\Repositories\DeveloperRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

class DeveloperController extends Controller
{
    public function __construct(
        private DeveloperRepository $developerRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $paginator = $this->developerRepository->getPaginated($request, 12);

        return Inertia::render('Welcome', [
            'canRegister' => Features::enabled(Features::registration()),
            'developers' => DeveloperResource::collection($paginator->items())->resolve(),
            'filterSearch' => $request->input('filter.search'),
            'filters' => [
                'search' => ($request->query('filter') ?? [])['search'] ?? null,
                'name' => ($request->query('filter') ?? [])['name'] ?? null,
                'job_title.name' => ($request->query('filter') ?? [])['job_title.name'] ?? null,
                'skill' => ($request->query('filter') ?? [])['skill'] ?? null,
                'years_min' => ($request->query('filter') ?? [])['years_min'] ?? null,
                'years_max' => ($request->query('filter') ?? [])['years_max'] ?? null,
            ],
            'sort' => $request->input('sort', 'name'),
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

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response|RedirectResponse
    {
        return Inertia::render('Developers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('home');
    }

    /**
     * Display the specified resource (developer profile by slug).
     */
    public function show(Developer $developer): Response
    {
        $developer->load([
            'jobTitle',
            'skills',
            'badges',
            'companies' => fn ($q) => $q
                ->withoutGlobalScope(DeveloperScope::class)
                ->topLevel()
                ->visible()
                ->with(['jobTitle:id,name', 'children' => fn ($c) => $c->withoutGlobalScope(DeveloperScope::class)->visible()->with('jobTitle:id,name')])
                ->orderByDesc('start_date'),
            'recommendationsReceived' => fn ($q) => $q
                ->where('status', RecommendationStatus::APPROVED)
                ->with('recommender:id,name,job_title_id', 'recommender.jobTitle:id,name'),
            'projects' => fn ($q) => $q
                ->withoutGlobalScope(DeveloperScope::class)
                ->visible(),
        ]);
        $developer->loadCount('recommendationsReceived');

        return Inertia::render('Developers/Show', [
            'developer' => (new DeveloperResource($developer))->resolve(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): Response|RedirectResponse
    {
        return redirect()->route('home');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        return redirect()->route('home');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        return redirect()->route('home');
    }
}
