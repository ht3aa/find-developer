<?php

namespace App\Http\Controllers;

use App\Enums\RecommendationStatus;
use App\Models\Developer;

class DeveloperProfileController extends Controller
{
    public function show($slug)
    {
        $developer = Developer::with([
            'jobTitle',
            'skills',
            'badges',
            'projects' => function ($query) {
                $query->where('show_project', true)
                    ->orderBy('created_at', 'desc');
            },
            'recommendationsReceived' => function ($query) {
                $query->where('status', RecommendationStatus::APPROVED)
                    ->with('recommender.jobTitle')
                    ->orderBy('created_at', 'desc');
            },
        ])
        ->withCount('projects')
        ->where('slug', $slug)
        ->firstOrFail();

        return view('developer-profile', compact('developer'));
    }
}
