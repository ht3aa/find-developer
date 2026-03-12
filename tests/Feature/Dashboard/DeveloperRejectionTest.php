<?php

use App\Enums\DeveloperStatus;
use App\Models\Developer;
use App\Models\JobTitle;
use App\Models\User;
use App\Notifications\DeveloperRejectedNotification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;

test('rejecting developer requires rejection reason and sends email', function () {
    Notification::fake();

    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $jobTitle = JobTitle::query()->first()
        ?? JobTitle::create(['name' => 'Developer', 'slug' => 'developer', 'is_active' => true]);

    $developer = Developer::factory()->create([
        'name' => 'Test Developer',
        'email' => 'developer@test.com',
        'status' => DeveloperStatus::PENDING,
        'job_title_id' => $jobTitle->id,
        'user_id' => null,
    ]);

    $response = $this->actingAs($admin)->put(route('developers.update', $developer->id), [
        'user_id' => null,
        'name' => $developer->name,
        'email' => $developer->email,
        'phone' => $developer->phone,
        'job_title_id' => $jobTitle->id,
        'years_of_experience' => $developer->years_of_experience,
        'bio' => $developer->bio,
        'portfolio_url' => $developer->portfolio_url,
        'github_url' => $developer->github_url,
        'linkedin_url' => $developer->linkedin_url,
        'youtube_url' => $developer->youtube_url,
        'is_available' => $developer->is_available,
        'availability_type' => [],
        'skill_names' => [],
        'badge_names' => [],
        'status' => 'rejected',
        'rejection_reason' => 'Profile does not meet our quality standards.',
        'recommended_by_us' => false,
    ]);

    $response->assertRedirect(route('developers.index'));

    $developer->refresh();
    expect($developer->status)->toBe(DeveloperStatus::REJECTED);

    Notification::assertSentTo(
        $developer,
        DeveloperRejectedNotification::class,
        fn (DeveloperRejectedNotification $n) => $n->reason === 'Profile does not meet our quality standards.'
    );
});

test('rejecting developer without reason fails validation', function () {
    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $jobTitle = JobTitle::query()->first()
        ?? JobTitle::create(['name' => 'Developer', 'slug' => 'developer', 'is_active' => true]);

    $developer = Developer::factory()->create([
        'name' => 'Test Developer',
        'email' => 'developer2@test.com',
        'status' => DeveloperStatus::PENDING,
        'job_title_id' => $jobTitle->id,
        'user_id' => null,
    ]);

    $response = $this->actingAs($admin)->put(route('developers.update', $developer->id), [
        'user_id' => null,
        'name' => $developer->name,
        'email' => $developer->email,
        'phone' => $developer->phone,
        'job_title_id' => $jobTitle->id,
        'years_of_experience' => $developer->years_of_experience,
        'bio' => $developer->bio,
        'portfolio_url' => $developer->portfolio_url,
        'github_url' => $developer->github_url,
        'linkedin_url' => $developer->linkedin_url,
        'youtube_url' => $developer->youtube_url,
        'is_available' => $developer->is_available,
        'availability_type' => [],
        'skill_names' => [],
        'badge_names' => [],
        'status' => 'rejected',
        'rejection_reason' => '',
        'recommended_by_us' => false,
    ]);

    $response->assertSessionHasErrors('rejection_reason');
});

test('updating already rejected developer does not require rejection reason', function () {
    Notification::fake();

    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $jobTitle = JobTitle::query()->first()
        ?? JobTitle::create(['name' => 'Developer', 'slug' => 'developer', 'is_active' => true]);

    $developer = Developer::factory()->create([
        'name' => 'Test Developer',
        'email' => 'developer3@test.com',
        'status' => DeveloperStatus::REJECTED,
        'job_title_id' => $jobTitle->id,
        'user_id' => null,
    ]);

    $response = $this->actingAs($admin)->put(route('developers.update', $developer->id), [
        'user_id' => null,
        'name' => 'Updated Name',
        'email' => $developer->email,
        'phone' => $developer->phone,
        'job_title_id' => $jobTitle->id,
        'years_of_experience' => $developer->years_of_experience,
        'bio' => $developer->bio,
        'portfolio_url' => $developer->portfolio_url,
        'github_url' => $developer->github_url,
        'linkedin_url' => $developer->linkedin_url,
        'youtube_url' => $developer->youtube_url,
        'is_available' => $developer->is_available,
        'availability_type' => [],
        'skill_names' => [],
        'badge_names' => [],
        'status' => 'rejected',
        'recommended_by_us' => false,
    ]);

    $response->assertRedirect(route('developers.index'));
    Notification::assertNothingSent();
});
