<?php

use App\Http\Controllers\DeveloperController;
use Illuminate\Support\Facades\Route;

test('returns a successful response', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
});

test('home route targets DeveloperController', function () {
    $route = Route::getRoutes()->getByName('home');

    expect($route)->not->toBeNull()
        ->and($route->getControllerClass())->toBe(DeveloperController::class);
});
