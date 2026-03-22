<?php

use App\Enums\RecommendationStatus;
use App\Models\Developer;
use App\Models\DeveloperRecommendation;
use App\Models\User;
use Illuminate\Support\Facades\Config;

test('super admin can view the filament developer recommendations index', function () {
    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get('/admin/developer-recommendations')
        ->assertSuccessful();
});

test('super admin can view the filament edit developer recommendation page', function () {
    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $recommender = Developer::factory()->create();
    $recommended = Developer::factory()->create();
    $recommendation = DeveloperRecommendation::create([
        'recommender_id' => $recommender->id,
        'recommended_id' => $recommended->id,
        'recommendation_note' => 'Solid engineer.',
        'status' => RecommendationStatus::PENDING,
    ]);

    $this->actingAs($admin)
        ->get('/admin/developer-recommendations/'.$recommendation->id.'/edit')
        ->assertSuccessful();
});
