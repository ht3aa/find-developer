<?php

namespace App\Http\Controllers;

use App\Enums\RecommendationStatus;
use App\Http\Requests\StoreDeveloperRecommendationRequest;
use App\Http\Resources\DeveloperResource;
use App\Models\Developer;
use App\Models\DeveloperRecommendation;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DeveloperRecommendationController extends Controller
{
    /**
     * Show the recommend form for a developer.
     */
    public function show(Developer $developer): Response|RedirectResponse
    {
        $user = request()->user();
        if (! $user?->developer) {
            return redirect()->route('home')
                ->with('error', 'You need a developer profile to recommend others.');
        }
        if ($user->developer->id === $developer->id) {
            return redirect()->route('developers.show', $developer->slug);
        }
        if ($user->developer->recommendationsGiven()->where('recommended_id', $developer->id)->exists()) {
            return redirect()->route('developers.show', $developer->slug)
                ->with('info', 'You have already recommended this developer.');
        }

        $developer->load(['jobTitle']);

        return Inertia::render('Developers/Recommend', [
            'developer' => (new DeveloperResource($developer))->resolve(),
            'storeUrl' => route('developers.recommendations.store', $developer->slug),
        ]);
    }

    /**
     * Store a new recommendation for a developer.
     */
    public function store(StoreDeveloperRecommendationRequest $request, Developer $developer): RedirectResponse
    {
        $recommender = $request->user()->developer;
        if ($recommender->recommendationsGiven()->where('recommended_id', $developer->id)->exists()) {
            return redirect()->route('developers.show', $developer->slug)
                ->with('info', 'You have already recommended this developer.');
        }

        DeveloperRecommendation::create([
            'recommender_id' => $recommender->id,
            'recommended_id' => $developer->id,
            'recommendation_note' => $request->validated('recommendation_note'),
            'status' => RecommendationStatus::PENDING,
        ]);

        return redirect()->route('developers.show', $developer->slug)
            ->with('success', 'Your recommendation has been submitted and is pending approval.');
    }
}
