<?php

use App\Http\Controllers\DeveloperController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [DeveloperController::class, 'index'])->name('home');
Route::get('developers/{developer:slug}', [DeveloperController::class, 'show'])->name('developers.show');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/settings.php';
