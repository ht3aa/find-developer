<?php

use App\Models\User;
use Illuminate\Support\Facades\Config;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    Permission::findOrCreate('View:DeveloperProfile');
    $user = User::factory()->create();
    $user->givePermissionTo('View:DeveloperProfile');

    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('super admin receives viewAdminPanel in shared auth', function () {
    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('auth.can.viewAdminPanel', true));
});

test('non super admin does not receive viewAdminPanel', function () {
    Permission::findOrCreate('View:DeveloperProfile');
    $user = User::factory()->create([
        'email' => 'member@test.com',
        'email_verified_at' => now(),
    ]);
    $user->givePermissionTo('View:DeveloperProfile');

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('auth.can.viewAdminPanel', false));
});
