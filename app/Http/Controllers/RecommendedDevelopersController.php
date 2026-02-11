<?php

namespace App\Http\Controllers;

use App\Enums\RecommendationStatus;
use App\Models\Developer;
use App\Models\Scopes\DeveloperScope;

class RecommendedDevelopersController extends Controller
{
    public function index()
    {
        $isAdmin = auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin());
        $isLoggedIn = auth()->check();

        $developers = Developer::with(['jobTitle', 'skills', 'badges'])
            ->with(['projects' => function ($query) {
                $query->withoutGlobalScopes([DeveloperScope::class])
                    ->where('show_project', true)
                    ->limit(6)
                    ->orderBy('created_at', 'desc');
            }])
            ->withCount(['projects' => function ($query) {
                $query->withoutGlobalScopes([DeveloperScope::class])
                    ->where('show_project', true);
            }])
            ->withCount(['recommendationsReceived' => function ($query) {
                $query->where('status', RecommendationStatus::APPROVED);
            }])
            ->recommended()
            ->orderBy('recommendations_received_count', 'desc')
            ->get();

        $developersData = $developers->map(function ($dev) use ($isAdmin) {
            return [
                'id' => $dev->id,
                'name' => $dev->name,
                'slug' => $dev->slug,
                'profile_url' => $dev->slug ? route('developer.profile', $dev->slug) : null,
                'projects_url' => $dev->slug ? route('developer.projects', $dev->slug) : null,
                'job_title' => $dev->jobTitle?->name,
                'years_of_experience' => $dev->years_of_experience,
                'location' => $dev->location?->getLabel(),
                'phone' => $dev->phone,
                'email' => $dev->email,
                'bio' => $dev->bio,
                'is_available' => $dev->is_available,
                'is_premium' => $dev->isPremium(),
                'availability_type' => $dev->availability_type
                    ? collect($dev->availability_type)->map(fn ($t) => ['value' => $t->value, 'label' => $t->getLabel()])->values()
                    : [],
                'has_salary' => (bool) ($dev->expected_salary_from || $dev->expected_salary_to),
                'expected_salary_from' => $isAdmin ? $dev->expected_salary_from : null,
                'expected_salary_to' => $isAdmin ? $dev->expected_salary_to : null,
                'currency' => $isAdmin ? $dev->salary_currency?->value : null,
                'portfolio_url' => $dev->portfolio_url,
                'github_url' => $dev->github_url,
                'linkedin_url' => $dev->linkedin_url,
                'recommendations_received_count' => $dev->recommendations_received_count,
                'projects_count' => $dev->projects_count,
                'badges' => $dev->badges->map(fn ($b) => [
                    'id' => $b->id,
                    'name' => $b->name,
                    'icon' => $b->icon,
                    'color' => $b->color,
                ]),
                'skills' => $dev->skills->pluck('name')->values(),
                'projects' => $dev->projects->map(fn ($p) => [
                    'title' => $p->title,
                    'description' => $p->description,
                    'link' => $p->link,
                ]),
            ];
        });

        return view('recommended-developers', [
            'developersData' => $developersData,
            'isAdmin' => $isAdmin,
            'isLoggedIn' => $isLoggedIn,
        ]);
    }
}
