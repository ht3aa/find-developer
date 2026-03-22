<?php

use App\Enums\AvailabilityType;
use App\Enums\OfferStatus;
use App\Models\Developer;
use App\Models\DeveloperOffer;
use App\Models\JobTitle;
use App\Models\User;
use Illuminate\Support\Facades\Config;

test('super admin can view the filament developer offers index', function () {
    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get('/admin/developer-offers')
        ->assertSuccessful();
});

test('super admin can view the filament edit developer offer page', function () {
    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $user = User::factory()->create();
    $developer = Developer::factory()->create();
    $jobTitle = JobTitle::create([
        'name' => 'Test Engineer '.uniqid(),
        'slug' => 'test-engineer-'.uniqid(),
        'description' => null,
        'is_active' => true,
    ]);

    $offer = DeveloperOffer::create([
        'developer_ids' => [$developer->id],
        'user_id' => $user->id,
        'company_name' => 'Acme',
        'job_title_id' => $jobTitle->id,
        'message' => 'Hello',
        'salary_range' => null,
        'work_type' => AvailabilityType::FULL_TIME,
        'contact_email' => 'hr@acme.test',
        'status' => OfferStatus::PENDING,
    ]);

    $this->actingAs($admin)
        ->get('/admin/developer-offers/'.$offer->id.'/edit')
        ->assertSuccessful();
});
