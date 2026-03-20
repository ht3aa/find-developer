<?php

use App\Models\Developer;
use App\Models\JobTitle;

it('combines multiple preset ids with or semantics', function () {
    $fe = JobTitle::query()->create([
        'name' => 'Frontend Developer',
        'slug' => 'frontend-developer-preset-or',
        'is_active' => true,
    ]);
    $be = JobTitle::query()->create([
        'name' => 'Backend Developer',
        'slug' => 'backend-developer-preset-or',
        'is_active' => true,
    ]);

    Developer::factory()->create([
        'name' => 'Preset Or Mid BE',
        'job_title_id' => $be->id,
        'years_of_experience' => 4,
    ]);
    Developer::factory()->create([
        'name' => 'Preset Or Senior FE',
        'job_title_id' => $fe->id,
        'years_of_experience' => 7,
    ]);
    Developer::factory()->create([
        'name' => 'Preset Or Junior FE',
        'job_title_id' => $fe->id,
        'years_of_experience' => 1,
    ]);

    $response = $this->getJson('/api/developers?per_page=50&filter[preset_ids]=be-mid,fe-senior');

    $response->assertSuccessful();
    $names = collect($response->json('data'))->pluck('name')->all();
    expect($names)->toContain('Preset Or Mid BE')
        ->toContain('Preset Or Senior FE')
        ->not->toContain('Preset Or Junior FE');
});
