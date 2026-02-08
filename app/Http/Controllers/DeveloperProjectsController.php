<?php

namespace App\Http\Controllers;

use App\Enums\SubscriptionPlan;
use App\Models\Developer;
use App\Models\Scopes\DeveloperScope;

class DeveloperProjectsController extends Controller
{
    public function show($developerSlug)
    {

        $developer = Developer::with(['jobTitle', 'projects' => function ($query) {
            $query->withoutGlobalScopes([DeveloperScope::class])
                ->where('show_project', true)
                ->orderBy('created_at', 'desc');
        }])->where('slug', $developerSlug)->firstOrFail();

        if ($developer->subscription_plan !== SubscriptionPlan::PREMIUM && ! $developer->recommended_by_us) {
            return redirect()->route('home');
        }

        return view('developer-projects', compact('developer'));
    }
}
