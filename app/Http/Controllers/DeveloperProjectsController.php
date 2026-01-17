<?php

namespace App\Http\Controllers;

use App\Enums\SubscriptionPlan;
use App\Models\Developer;
use App\Models\Scopes\DeveloperScope;
use Illuminate\Http\Request;

class DeveloperProjectsController extends Controller
{
    public function show($developerSlug)
    {

        $developer = Developer::with(['jobTitle', 'projects' => function ($query) {
            $query->withoutGlobalScopes([DeveloperScope::class])->orderBy('created_at', 'desc');
        }])->where('slug', $developerSlug)->firstOrFail();


        if ($developer->subscription_plan !== SubscriptionPlan::PREMIUM) {
            return redirect()->route('home');
        }

        return view('developer-projects', compact('developer'));
    }
}
