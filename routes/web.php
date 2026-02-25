<?php

use App\Http\Controllers\BadgeController;
use App\Http\Controllers\Dashboard\DeveloperController as DashboardDeveloperController;
use App\Http\Controllers\Dashboard\DeveloperProfileController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\DeveloperRecommendationController;
use App\Http\Controllers\PublicBadgeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [DeveloperController::class, 'index'])->name('home');
Route::get('badges', [PublicBadgeController::class, 'index'])->name('badges.public');
Route::get('developers/{developer:slug}', [DeveloperController::class, 'show'])->name('developers.show');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('developers/{developer:slug}/recommend', [DeveloperRecommendationController::class, 'show'])->name('developers.recommend');
    Route::post('developers/{developer:slug}/recommend', [DeveloperRecommendationController::class, 'store'])->name('developers.recommendations.store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::prefix('dashboard')->group(function () {
        Route::resource('badges', BadgeController::class)->except(['show']);
        Route::resource('developers', DashboardDeveloperController::class)
            ->except(['show']);

        Route::get('developer-profile', [DeveloperProfileController::class, 'index'])
            ->name('dashboard.developer-profile.index');
        Route::put('developer-profile', [DeveloperProfileController::class, 'update'])
            ->name('dashboard.developer-profile.update');
    });
});

require __DIR__ . '/settings.php';
