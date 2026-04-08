<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users with dashboard access can visit the dashboard', function () {
    Permission::findOrCreate('View:DeveloperProfile', 'web');
    $user = User::factory()->create();
    $user->givePermissionTo('View:DeveloperProfile');
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});
