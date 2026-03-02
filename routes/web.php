<?php

use App\Http\Controllers\BadgeController;
use App\Http\Controllers\ChartsController;
use App\Http\Controllers\Dashboard\ActivityLogController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\DeveloperBlogController;
use App\Http\Controllers\Dashboard\DeveloperController as DashboardDeveloperController;
use App\Http\Controllers\Dashboard\DeveloperProfileController;
use App\Http\Controllers\Dashboard\DeveloperProjectController;
use App\Http\Controllers\Dashboard\DeveloperRecommendationController as DashboardDeveloperRecommendationController;
use App\Http\Controllers\Dashboard\NewsletterController as DashboardNewsletterController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\WorkExperienceController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\DeveloperRecommendationController;
use App\Http\Controllers\HackathonAttendanceController;
use App\Http\Controllers\HackathonController;
use App\Http\Controllers\HackathonSubscribeController;
use App\Http\Controllers\HackathonSubscribersController;
use App\Http\Controllers\HackathonTeamController;
use App\Http\Controllers\HackathonTeamMemberController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PublicBadgeController;
use App\Http\Controllers\PublicBlogController;
use App\Http\Controllers\PublicHackathonController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', function () {
    $sitemapUrl = rtrim(config('app.url'), '/') . '/sitemap.xml';

    return response("User-agent: *\nAllow: /\n\nSitemap: {$sitemapUrl}\n", 200, [
        'Content-Type' => 'text/plain',
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->name('robots');

Route::get('/', [DeveloperController::class, 'index'])->name('home');
Route::post('newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');
Route::get('badges', [PublicBadgeController::class, 'index'])->name('badges.public');
Route::get('blogs', [PublicBlogController::class, 'index'])->name('blogs.public.index');
Route::get('blogs/{slug}', [PublicBlogController::class, 'show'])->name('blogs.public.show')->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*');
Route::get('hackathons', [PublicHackathonController::class, 'index'])->name('hackathons.public');
Route::get('hackathons/{hackathon:slug}', [PublicHackathonController::class, 'show'])->name('hackathons.show');
Route::get('hackathons/{hackathon:slug}/teams', [PublicHackathonController::class, 'teams'])
    ->name('hackathons.teams.public');
Route::get('developers/{developer:slug}', [DeveloperController::class, 'show'])->name('developers.show');
Route::get('charts', [ChartsController::class, 'index'])->name('charts.public');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('developers/{developer:slug}/recommend', [DeveloperRecommendationController::class, 'show'])->name('developers.recommend');
    Route::post('developers/{developer:slug}/recommend', [DeveloperRecommendationController::class, 'store'])->name('developers.recommendations.store');
    Route::post('hackathons/{hackathon:slug}/subscribe', HackathonSubscribeController::class)->name('hackathons.subscribe');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('dashboard')->group(function () {
        Route::resource('badges', BadgeController::class)->except(['show']);
        Route::get('hackathons/{hackathon}/attendance', [HackathonAttendanceController::class, 'index'])->name('hackathons.attendance.index');
        Route::patch('hackathons/{hackathon}/attendance', [HackathonAttendanceController::class, 'update'])->name('hackathons.attendance.update');
        Route::get('hackathons/{hackathon}/subscribers/create', [HackathonSubscribersController::class, 'create'])->name('hackathons.subscribers.create');
        Route::post('hackathons/{hackathon}/subscribers', [HackathonSubscribersController::class, 'store'])->name('hackathons.subscribers.store');
        Route::get('hackathons/{hackathon}/subscribers/{subscriber}/edit', [HackathonSubscribersController::class, 'edit'])->name('hackathons.subscribers.edit');
        Route::put('hackathons/{hackathon}/subscribers/{subscriber}', [HackathonSubscribersController::class, 'update'])->name('hackathons.subscribers.update');
        Route::get('hackathons/{hackathon}/subscribers', [HackathonSubscribersController::class, 'index'])->name('hackathons.subscribers.index');
        Route::get('hackathons/{hackathon}/teams', [HackathonTeamController::class, 'index'])->name('hackathons.teams.index');
        Route::get('hackathons/{hackathon}/teams/create', [HackathonTeamController::class, 'create'])->name('hackathons.teams.create');
        Route::post('hackathons/{hackathon}/teams', [HackathonTeamController::class, 'store'])->name('hackathons.teams.store');
        Route::get('hackathons/{hackathon}/teams/{team}/edit', [HackathonTeamController::class, 'edit'])->name('hackathons.teams.edit');
        Route::put('hackathons/{hackathon}/teams/{team}', [HackathonTeamController::class, 'update'])->name('hackathons.teams.update');
        Route::delete('hackathons/{hackathon}/teams/{team}', [HackathonTeamController::class, 'destroy'])->name('hackathons.teams.destroy');
        Route::get('hackathons/{hackathon}/teams/{team}/members', [HackathonTeamMemberController::class, 'index'])->name('hackathons.teams.members.index');
        Route::get('hackathons/{hackathon}/teams/{team}/members/create', [HackathonTeamMemberController::class, 'create'])->name('hackathons.teams.members.create');
        Route::post('hackathons/{hackathon}/teams/{team}/members', [HackathonTeamMemberController::class, 'store'])->name('hackathons.teams.members.store');
        Route::get('hackathons/{hackathon}/teams/{team}/members/{member}/edit', [HackathonTeamMemberController::class, 'edit'])->name('hackathons.teams.members.edit');
        Route::put('hackathons/{hackathon}/teams/{team}/members/{member}', [HackathonTeamMemberController::class, 'update'])->name('hackathons.teams.members.update');
        Route::delete('hackathons/{hackathon}/teams/{team}/members/{member}', [HackathonTeamMemberController::class, 'destroy'])->name('hackathons.teams.members.destroy');
        Route::resource('hackathons', HackathonController::class)->except(['show']);
        Route::post('developers/bulk-email', [DashboardDeveloperController::class, 'bulkEmail'])->name('developers.bulk-email');
        Route::post('developers/bulk-email-all', [DashboardDeveloperController::class, 'bulkEmailAll'])->name('developers.bulk-email-all');
        Route::resource('developers', DashboardDeveloperController::class)->except(['show']);
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show']);

        Route::get('developer-profile', [DeveloperProfileController::class, 'index'])
            ->name('dashboard.developer-profile.index');
        Route::put('developer-profile', [DeveloperProfileController::class, 'update'])
            ->name('dashboard.developer-profile.update');
        Route::get('developer-profile/cv', [DeveloperProfileController::class, 'downloadCv'])
            ->name('dashboard.developer-profile.download-cv');

        Route::resource('work-experience', WorkExperienceController::class)->except(['show']);
        Route::resource('developer-projects', DeveloperProjectController::class)->except(['show']);
        Route::resource('developer-blogs', DeveloperBlogController::class)->except(['show']);
        Route::resource('developer-recommendations', DashboardDeveloperRecommendationController::class)->only(['index', 'edit', 'update', 'destroy']);

        Route::get('newsletter', [DashboardNewsletterController::class, 'index'])->name('dashboard.newsletter.index');
        Route::post('newsletter/bulk-email', [DashboardNewsletterController::class, 'bulkEmail'])->name('dashboard.newsletter.bulk-email');
        Route::post('newsletter/bulk-email-all', [DashboardNewsletterController::class, 'bulkEmailAll'])->name('dashboard.newsletter.bulk-email-all');
        Route::get('activity-log', [ActivityLogController::class, 'index'])->name('dashboard.activity-log.index');
        Route::get('activity-log/{id}/properties', [ActivityLogController::class, 'properties'])->name('dashboard.activity-log.properties')->whereNumber('id');
        Route::get('activity-log/{id}', [ActivityLogController::class, 'show'])->name('dashboard.activity-log.show')->whereNumber('id');
    });
});

require __DIR__ . '/settings.php';
