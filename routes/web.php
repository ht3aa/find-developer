<?php

use App\Filament\Pages\CompanyJobRegistration;
use App\Filament\Pages\DeveloperRecommendation;
use App\Filament\Pages\DeveloperRegistration;
use App\Http\Controllers\DeveloperAuthController;
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

Route::get('/register', DeveloperRegistration::class)->name('register');

Route::get('/jobs', [JobsController::class, 'index'])->name('jobs');

Route::get('/get-experience', function () {
    $tasks = \App\Models\ExperienceTask::query()
        ->available()
        ->withCount('developers')
        ->orderByDesc('created_at')
        ->get()
        ->map(fn ($t) => [
            'id' => $t->id,
            'title' => $t->title,
            'description_plain' => strip_tags($t->description),
            'requirements' => $t->requirements,
            'rewards' => $t->rewards,
            'status' => $t->status->value,
            'price' => $t->price,
            'price_currency' => $t->price_currency?->value ?? 'IQD',
            'experience_gain' => $t->experience_gain?->value,
            'xp_value' => $t->experience_gain?->value ?? 0,
            'required_developers_count' => $t->required_developers_count,
            'developers_count' => $t->developers_count,
            'time_ago' => $t->created_at->diffForHumans(),
        ]);

    return view('experience-tasks', ['tasks' => $tasks]);
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
    $services = \App\Models\UserService::with(['user', 'appointments', 'badges'])
        ->withoutGlobalScopes([\App\Models\Scopes\UserScope::class])
        ->withCount('appointments')
        ->whereHas('user', function ($query) {
            $query->where('user_type', \App\Enums\UserType::HR);
        })
        ->where('is_active', true)
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy('user_id');

    $providers = $services->map(function ($userServices) {
        $user = $userServices->first()->user;
        return [
            'user' => [
                'name' => $user->name,
                'linkedin_url' => $user->linkedin_url,
            ],
            'services' => $userServices->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'description' => $s->description,
                'is_active' => $s->is_active,
                'price' => $s->price,
                'price_currency' => $s->price_currency?->value,
                'time_minutes' => $s->time_minutes,
                'appointments_count' => $s->appointments_count ?? 0,
                'badges' => $s->badges->map(fn ($b) => [
                    'id' => $b->id,
                    'name' => $b->name,
                    'icon' => $b->icon,
                    'color' => $b->color,
                ]),
            ])->values(),
        ];
    })->values();

    return view('services', ['providers' => $providers]);
})->name('services');

Route::get('/badges', function () {
    $badges = \App\Models\Badge::where('is_active', true)->orderBy('name')->get();
    return view('badges', ['badges' => $badges]);
})->name('badges');

Route::get('/charts', function () {
    return view('charts');
})->name('charts');

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

// Developer Authentication Routes
Route::get('/developer/login', [DeveloperAuthController::class, 'showLoginForm'])
    ->name('developer.login');
Route::post('/developer/login', [DeveloperAuthController::class, 'login']);
Route::post('/developer/logout', [DeveloperAuthController::class, 'logout'])
    ->name('developer.logout');

// Developer Recommendation Routes (requires authentication)
Route::get('/developer/{developer}/recommend', DeveloperRecommendation::class)
    ->name('developer.recommend');
