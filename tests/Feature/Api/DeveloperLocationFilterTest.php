<?php

use App\Enums\WorldGovernorate;
use App\Models\Developer;

it('lists locations for the filter dropdown', function () {
    $response = $this->getJson('/api/locations');

    $response->assertSuccessful();
    $data = $response->json('data');
    expect($data)->toBeArray()->not->toBeEmpty();
    expect($data[0])->toHaveKeys(['value', 'label']);
    expect(collect($data)->pluck('value')->all())->toContain(WorldGovernorate::BAGHDAD->value);
});

it('filters developers by location enum values', function () {
    Developer::factory()->create([
        'name' => 'In Baghdad',
        'location' => WorldGovernorate::BAGHDAD,
    ]);
    Developer::factory()->create([
        'name' => 'In Erbil',
        'location' => WorldGovernorate::ERBIL,
    ]);

    $query = http_build_query([
        'per_page' => 50,
        'filter' => [
            'location' => 'baghdad',
        ],
    ]);

    $response = $this->getJson('/api/developers?'.$query);

    $response->assertSuccessful();
    $names = collect($response->json('data'))->pluck('name')->all();
    expect($names)->toContain('In Baghdad')
        ->and($names)->not->toContain('In Erbil');
});

it('accepts comma-separated locations as OR', function () {
    Developer::factory()->create([
        'name' => 'Baghdad Dev',
        'location' => WorldGovernorate::BAGHDAD,
    ]);
    Developer::factory()->create([
        'name' => 'Erbil Dev',
        'location' => WorldGovernorate::ERBIL,
    ]);

    $query = http_build_query([
        'per_page' => 50,
        'filter' => [
            'location' => 'baghdad,erbil',
        ],
    ]);

    $response = $this->getJson('/api/developers?'.$query);

    $response->assertSuccessful();
    $names = collect($response->json('data'))->pluck('name')->all();
    expect($names)->toContain('Baghdad Dev')
        ->and($names)->toContain('Erbil Dev');
});
