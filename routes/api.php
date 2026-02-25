<?php

use App\Http\Controllers\Api\BadgeController;
use App\Http\Controllers\Api\DeveloperController;
use App\Http\Controllers\Api\JobTitleController;
use App\Http\Controllers\Api\SkillController;
use Illuminate\Support\Facades\Route;

Route::get('/developers', [DeveloperController::class, 'index'])->name('api.developers.index');
Route::get('/job-titles', [JobTitleController::class, 'index'])->name('api.job-titles.index');
Route::get('/skills', [SkillController::class, 'index'])->name('api.skills.index');
Route::get('/badges', [BadgeController::class, 'index'])->name('api.badges.index');
