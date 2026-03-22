<?php

namespace App\Http\Controllers;

use App\Enums\RecommendationStatus;
use App\Http\Resources\DeveloperResource;
use App\Models\Developer;
use App\Models\DeveloperProfileView;
use App\Models\Scopes\DeveloperScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

class DeveloperController extends Controller
{
    /**
     * Display the welcome page (shell). Developer data is loaded via API.
     */
    public function index(): Response
    {
        $user = request()->user();

        return Inertia::render('Welcome', [
            'canRegister' => Features::enabled(Features::registration()),
            'newsletterStoreUrl' => route('newsletter.store'),
            'developerOffersStoreUrl' => $user ? route('developer-offers.store') : null,
            'heroGreetingNote' => 'أيامكم سعيدة، وينعاد عليكم بالصحة والعافية، وإن شاء الله المنصة تكبر بوجودكم وتفيدكم أكثر فأكثر.',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('home');
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
        DeveloperProfileView::create(['developer_id' => $developer->id]);

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
