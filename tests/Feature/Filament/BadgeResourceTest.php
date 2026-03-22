<?php

use App\Models\User;
use Illuminate\Support\Facades\Config;

test('super admin can view the filament badges index', function () {
    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get('/admin/badges')
        ->assertSuccessful();
});
