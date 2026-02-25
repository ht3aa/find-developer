<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Developer;
use App\Models\Scopes\ApprovedScope;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with stats.
     */
    public function index(): Response
    {
        $this->authorize('viewDeveloperProfile', auth()->user()?->developer);

        return Inertia::render('Dashboard', [
            'stats' => [
                'developers' => Developer::withoutGlobalScope(ApprovedScope::class)->count(),
                'users' => User::count(),
                'badges' => Badge::count(),
            ],
        ]);
    }
}
