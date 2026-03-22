<?php

use App\Models\Hackathon;
use App\Models\User;
use Illuminate\Support\Facades\Config;

test('super admin can view the filament hackathons index', function () {
    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get('/admin/hackathons')
        ->assertSuccessful();
});

test('super admin can view the filament create hackathon page', function () {
    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get('/admin/hackathons/create')
        ->assertSuccessful();
});

test('super admin can view the filament nested teams index for a hackathon', function () {
    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $hackathon = Hackathon::factory()->create();

    $this->actingAs($admin)
        ->get('/admin/hackathons/'.$hackathon->getKey().'/teams')
        ->assertSuccessful();
});
