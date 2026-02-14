<?php

namespace App\Http\Controllers;

use App\Enums\RecommendationStatus;
use App\Models\Developer;
use App\Models\Scopes\DeveloperScope;

class DeveloperProfileController extends Controller
{
    public function show($slug)
    {
        $developer = Developer::with([
            'jobTitle',
            'skills',
            'badges',
            'projects' => function ($query) {
                $query->withoutGlobalScopes([DeveloperScope::class])
                    ->where('show_project', true)
                    ->orderBy('created_at', 'desc');
            },
            'recommendationsReceived' => function ($query) {
                $query->where('status', RecommendationStatus::APPROVED)
                    ->with('recommender.jobTitle')
                    ->orderBy('created_at', 'desc');
            },
        ])
            ->withCount([
                'projects' => function ($query) {
                    $query->withoutGlobalScopes([DeveloperScope::class])
                        ->where('show_project', true);
                },
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('developer-profile', compact('developer'));
    }
}
