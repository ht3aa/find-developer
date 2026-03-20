<?php

use App\Enums\DeveloperStatus;
use App\Models\Developer;

it('prints developer emails comma-separated in id order', function () {
    Developer::factory()->create(['email' => 'alpha@example.com']);
    Developer::factory()->create(['email' => 'beta@example.com']);

    $this->artisan('developers:list-emails')
        ->expectsOutput('alpha@example.com,beta@example.com')
        ->assertSuccessful();
});

it('includes developers excluded by the approved global scope', function () {
    Developer::factory()->create([
        'email' => 'pending@example.com',
        'status' => DeveloperStatus::PENDING,
    ]);

    $this->artisan('developers:list-emails')
        ->expectsOutput('pending@example.com')
        ->assertSuccessful();
});

it('omits empty string emails', function () {
    Developer::factory()->create(['email' => 'kept@example.com']);
    Developer::factory()->create(['email' => '']);

    $this->artisan('developers:list-emails')
        ->expectsOutput('kept@example.com')
        ->assertSuccessful();
});
