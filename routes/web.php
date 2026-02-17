<?php

use App\Filament\Pages\CompanyJobRegistration;
use App\Filament\Pages\DeveloperOffer;
use App\Filament\Pages\DeveloperRecommendation;
use App\Filament\Pages\DeveloperRegistration;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\DeveloperProfileController;
use App\Http\Controllers\DeveloperProjectsController;
use App\Http\Controllers\DeveloperRecommendationsViewController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\RecommendedDevelopersController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('search');
})->name('home');

Route::get('/special-needs-developers', function () {
    return view('special-needs');
})->name('special-needs-developers');

Route::get('/register', DeveloperRegistration::class)->name('register');

Route::get('/jobs', [JobsController::class, 'index'])->name('jobs');

Route::get('/get-experience', function () {
    return view('experience-tasks');
})->name('experience-tasks');

Route::get('/recommended', [RecommendedDevelopersController::class, 'index'])->name('recommended');

Route::get('/post-job', CompanyJobRegistration::class)->name('post-job');

Route::get('/plans', function () {
    return view('plans');
})->name('plans');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/services', function () {
    return view('services');
})->name('services');

Route::get('/badges', function () {
    return view('badges');
})->name('badges');

Route::get('/charts', function () {
    return view('charts');
})->name('charts');

Route::get('/blogs', function () {
    return view('blogs');
})->name('blogs');

Route::get('/blogs/{slug}', [BlogController::class, 'show'])
    ->name('blog.show');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/robots.txt', function () {
    return response("User-agent: *\nAllow: /\n\nSitemap: ".url('/sitemap.xml'), 200)
        ->header('Content-Type', 'text/plain');
})->name('robots');

Route::get('/developers/{slug}', [DeveloperProfileController::class, 'show'])
    ->name('developer.profile');

Route::get('/developer/{developerSlug}/projects', [DeveloperProjectsController::class, 'show'])
    ->name('developer.projects');

Route::get('/developer/{developerSlug}/recommendations', [DeveloperRecommendationsViewController::class, 'show'])
    ->name('developer.recommendations');

// Redirect old developer login URL to Filament admin login
Route::get('/developer/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('developer.login');

// Developer Recommendation Routes (requires authentication)
Route::get('/developer/{developer}/recommend', DeveloperRecommendation::class)
    ->middleware(['auth'])
    ->name('developer.recommend');

// Developer Offer Routes (requires authentication)
Route::get('/developer/{developerSlug}/offer', DeveloperOffer::class)
    ->middleware(['auth'])
    ->name('developer.offer');
