<?php

use App\Http\Controllers\BadgeController;
use App\Http\Controllers\DeveloperController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [DeveloperController::class, 'index'])->name('home');
Route::get('developers/{developer:slug}', [DeveloperController::class, 'show'])->name('developers.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('badges', BadgeController::class)->except(['show']);
});

require __DIR__.'/settings.php';
