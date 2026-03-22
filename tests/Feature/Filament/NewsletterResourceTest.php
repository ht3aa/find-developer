<?php

use App\Models\User;
use Illuminate\Support\Facades\Config;

test('guest cannot view filament newsletters index', function () {
    $this->get('/admin/newsletters')
        ->assertRedirect(route('filament.admin.auth.login'));
});

test('non super admin cannot view filament newsletters index', function () {
    $user = User::factory()->create(['email' => 'nonsuper@example.com']);
    $this->actingAs($user);

    $this->get('/admin/newsletters')
        ->assertForbidden();
});

test('super admin can view the filament newsletters index', function () {
    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get('/admin/newsletters')
        ->assertSuccessful();
});
