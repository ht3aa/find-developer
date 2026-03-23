<?php

use App\Models\Badge;
use App\Models\Developer;

it('filters developers by badge name matching the home page badge filter query', function () {
    $badge = Badge::factory()->create([
        'name' => 'UniqueBadgeFilterLinkName',
        'is_active' => true,
    ]);

    $withBadge = Developer::factory()->create(['name' => 'Has Badge Dev']);
    $withBadge->badges()->attach($badge);

    Developer::factory()->create(['name' => 'No Badge Dev']);

    $query = http_build_query([
        'per_page' => 50,
        'filter' => [
            'badge' => 'UniqueBadgeFilterLinkName',
        ],
    ]);

    $response = $this->getJson('/api/developers?'.$query);

    $response->assertSuccessful();
    $names = collect($response->json('data'))->pluck('name')->all();
    expect($names)->toContain('Has Badge Dev')
        ->and($names)->not->toContain('No Badge Dev');
});
