<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BadgeController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\ConversationMessageController;
use App\Http\Controllers\Api\DeveloperController;
use App\Http\Controllers\Api\JobTitleController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Middleware\OptionalSanctumAuth;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'store'])
    ->middleware('throttle:login')
    ->name('api.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])
        ->name('api.profile');
    Route::post('/logout', [AuthController::class, 'destroy'])
        ->name('api.logout');
});

Route::middleware(['web', OptionalSanctumAuth::class])->group(function () {
    Route::get('/developers', [DeveloperController::class, 'index'])->name('api.developers.index');
    Route::get('/job-titles', [JobTitleController::class, 'index'])->name('api.job-titles.index');
    Route::get('/skills', [SkillController::class, 'index'])->name('api.skills.index');
    Route::get('/badges', [BadgeController::class, 'index'])->name('api.badges.index');
    Route::get('/locations', [LocationController::class, 'index'])->name('api.locations.index');
    Route::get('/products', [ProductController::class, 'index'])->name('api.products.index');
});

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::get('/conversations', [ConversationController::class, 'index'])
        ->name('api.conversations.index');
    Route::get('/conversations/{conversation}/messages', [ConversationMessageController::class, 'index'])
        ->name('api.conversations.messages.index');
    Route::post('/conversations/{conversation}/messages', [ConversationMessageController::class, 'store'])
        ->name('api.conversations.messages.store');
});
