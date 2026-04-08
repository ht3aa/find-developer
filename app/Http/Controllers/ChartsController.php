<?php

namespace App\Http\Controllers;

use App\Services\DeveloperChartDataService;
use Inertia\Inertia;
use Inertia\Response;

class ChartsController extends Controller
{
    /**
     * Display the public developer charts page.
     */
    public function index(DeveloperChartDataService $chartData): Response
    {
        return Inertia::render('Charts/Index', [
            'developersByLocation' => $chartData->developersByLocation(),
            'developersByAvailabilityType' => $chartData->developersByAvailabilityType(),
            'averageSalaryByExperience' => $chartData->averageSalaryByExperience(),
            'developersByJobTitle' => $chartData->developersByJobTitle(),
        ]);
    }
}
