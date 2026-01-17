<?php

use App\Filament\Pages\DeveloperRegistration;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('search');
})->name('home');

Route::get('/register', DeveloperRegistration::class)->name('register');

Route::get('/plans', function () {
    return view('plans');
})->name('plans');

Route::get('/about', function () {
    return view('about');
})->name('about');
