<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeveloperResource;
use App\Models\Developer;
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
        $developer->load(['jobTitle']);

        return Inertia::render('Developers/Recommend', [
            'developer' => (new DeveloperResource($developer))->resolve(),
        ]);
    }
}
