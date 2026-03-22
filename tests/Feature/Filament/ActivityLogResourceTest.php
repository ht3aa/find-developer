<?php

use App\Models\User;
use Illuminate\Support\Facades\Config;
use Spatie\Activitylog\Models\Activity;

test('super admin can view the filament activity log index', function () {
    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get('/admin/activity-logs')
        ->assertSuccessful();
});

test('super admin can view a single activity log entry', function () {
    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $activity = Activity::create([
        'description' => 'Feature test activity',
        'log_name' => 'default',
    ]);

    $this->actingAs($admin)
        ->get('/admin/activity-logs/'.$activity->id)
        ->assertSuccessful();
});
