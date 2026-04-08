<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Developer;
use App\Services\DeveloperChartDataService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with developer charts.
     */
    public function index(DeveloperChartDataService $chartData): Response
    {
        $this->authorize(
            'viewDeveloperProfile',
            auth()->user()?->developer ?? Developer::class,
        );

        return Inertia::render('Dashboard', [
            'developersByLocation' => $chartData->developersByLocation(),
            'developersByAvailabilityType' => $chartData->developersByAvailabilityType(),
            'averageSalaryByExperience' => $chartData->averageSalaryByExperience(),
            'developersByJobTitle' => $chartData->developersByJobTitle(),
        ]);
    }
}
