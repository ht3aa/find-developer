<?php

use App\Models\Developer;
use App\Models\JobTitle;

it('combines role_bands json entries with or semantics and ignores inactive titles', function () {
    $fe = JobTitle::query()->create([
        'name' => 'Frontend Developer',
        'slug' => 'frontend-developer-role-bands',
        'is_active' => true,
    ]);
    $be = JobTitle::query()->create([
        'name' => 'Backend Developer',
        'slug' => 'backend-developer-role-bands',
        'is_active' => true,
    ]);
    JobTitle::query()->create([
        'name' => 'Inactive Title',
        'slug' => 'inactive-title-role-bands',
        'is_active' => false,
    ]);

    Developer::factory()->create([
        'name' => 'Role Bands Mid BE',
        'job_title_id' => $be->id,
        'years_of_experience' => 4,
    ]);
    Developer::factory()->create([
        'name' => 'Role Bands Senior FE',
        'job_title_id' => $fe->id,
        'years_of_experience' => 7,
    ]);
    Developer::factory()->create([
        'name' => 'Role Bands Junior FE',
        'job_title_id' => $fe->id,
        'years_of_experience' => 1,
    ]);

    $bands = json_encode([
        ['job_title' => 'Backend Developer', 'years_min' => 3, 'years_max' => 5],
        ['job_title' => 'Frontend Developer', 'years_min' => 6, 'years_max' => null],
        ['job_title' => 'Inactive Title', 'years_min' => null, 'years_max' => 2],
    ], JSON_THROW_ON_ERROR);

    $encoded = base64_encode($bands);

    $response = $this->getJson('/api/developers?per_page=50&filter[role_bands]='.rawurlencode($encoded));

    $response->assertSuccessful();
    $response->assertJsonCount(2, 'data');
    $names = collect($response->json('data'))->pluck('name')->all();
    expect($names)->toContain('Role Bands Mid BE')
        ->toContain('Role Bands Senior FE')
        ->not->toContain('Role Bands Junior FE');
});
