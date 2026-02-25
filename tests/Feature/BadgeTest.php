<?php

use App\Models\User;

uses()->group('badges');

test('guests are redirected to login when visiting badges', function () {
    $response = $this->get(route('badges.index'));
    $response->assertRedirect(route('login'));
});

test('authenticated super admin can visit badges index', function () {
    $user = User::factory()->create(['email' => 'superadmin@test.com']);
    $this->actingAs($user);

    $response = $this->get(route('badges.index'));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Badges/Index')
        ->has('badges')
    );
});

test('authenticated super admin can create a badge', function () {
    $user = User::factory()->create(['email' => 'superadmin@test.com']);
    $this->actingAs($user);

    $response = $this->post(route('badges.store'), [
        'name' => 'Laravel Expert',
        'description' => 'Expert in Laravel framework',
        'color' => '#FF2D20',
        'is_active' => true,
    ]);

    $response->assertRedirect(route('badges.index'));
    $this->assertDatabaseHas('badges', ['name' => 'Laravel Expert']);
});
