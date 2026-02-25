<?php

use App\Http\Controllers\Api\DeveloperController;
use Illuminate\Support\Facades\Route;

Route::get('/developers', [DeveloperController::class, 'index'])->name('api.developers.index');
