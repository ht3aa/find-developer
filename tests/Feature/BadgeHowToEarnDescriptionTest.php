<?php

use App\Models\Badge;
use Inertia\Testing\AssertableInertia as Assert;

it('persists how_to_earn_description on the badge model', function () {
    $badge = Badge::factory()->create([
        'how_to_earn_description' => 'Complete the Laravel course.',
    ]);

    expect($badge->fresh()->how_to_earn_description)->toBe('Complete the Laravel course.');
});

it('exposes how_to_earn_description on the public badges catalog', function () {
    $badge = Badge::factory()->create([
        'is_active' => true,
        'how_to_earn_description' => 'Win a hackathon.',
    ]);

    $this->get(route('badges.public'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Badges/Public')
            ->has('badges', 1)
            ->where('badges.0.id', $badge->id)
            ->where('badges.0.how_to_earn_description', 'Win a hackathon.'));
});
