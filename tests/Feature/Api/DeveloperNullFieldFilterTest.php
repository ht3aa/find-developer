<?php

use App\Models\Developer;
use App\Models\JobTitle;
use App\Models\User;

it('does not apply null_field filter for guests', function () {
    $jobTitle = JobTitle::query()->create([
        'name' => 'Null Filter Test',
        'slug' => 'null-filter-test-job',
        'is_active' => true,
    ]);

    Developer::factory()->create([
        'name' => 'Has Phone',
        'job_title_id' => $jobTitle->id,
        'phone' => '+1234567890',
    ]);
    Developer::factory()->create([
        'name' => 'No Phone',
        'job_title_id' => $jobTitle->id,
        'phone' => null,
    ]);

    $response = $this->getJson('/api/developers?per_page=50&filter[null_field]=phone');

    $response->assertSuccessful();
    expect($response->json('meta.total'))->toBe(2);
});

it('filters by null field for super admin', function () {
    $superEmail = 'super-null-field@test.com';
    config(['app.super_admin_emails' => $superEmail]);

    $user = User::factory()->create(['email' => $superEmail]);
    $this->actingAs($user);

    $jobTitle = JobTitle::query()->create([
        'name' => 'Null Filter Test',
        'slug' => 'null-filter-test-job-2',
        'is_active' => true,
    ]);

    Developer::factory()->create([
        'name' => 'Has Phone',
        'job_title_id' => $jobTitle->id,
        'phone' => '+1234567890',
    ]);
    Developer::factory()->create([
        'name' => 'No Phone',
        'job_title_id' => $jobTitle->id,
        'phone' => null,
    ]);

    $response = $this->getJson('/api/developers?per_page=50&filter[null_field]=phone');

    $response->assertSuccessful();
    expect($response->json('meta.total'))->toBe(1);
    expect($response->json('data.0.name'))->toBe('No Phone');
});
