<?php

use App\Filament\Pages\DeveloperRegistration;
use App\Filament\Pages\CompanyJobRegistration;
use App\Http\Controllers\DeveloperProjectsController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\RecommendedDevelopersController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('search');
})->name('home');

Route::get('/register', DeveloperRegistration::class)->name('register');

Route::get('/jobs', [JobsController::class, 'index'])->name('jobs');

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

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/robots.txt', function () {
    return response("User-agent: *\nAllow: /\n\nSitemap: " . url('/sitemap.xml'), 200)
        ->header('Content-Type', 'text/plain');
})->name('robots');

Route::get('/developer/{developerSlug}/projects', [DeveloperProjectsController::class, 'show'])
    ->name('developer.projects');
