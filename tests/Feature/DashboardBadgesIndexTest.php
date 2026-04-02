<?php

use App\Models\Badge;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('loads dashboard badges index for super admin with developer counts', function () {
    $superEmail = 'superadmin-badges-index@example.com';
    config(['app.super_admin_emails' => $superEmail]);
    $user = User::factory()->create(['email' => $superEmail]);
    $this->actingAs($user);

    $badge = Badge::factory()->create(['name' => 'Dashboard Badge Index']);

    $response = $this->get(route('badges.index'));

    $response->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('Badges/Index')
        ->has('badges', 1)
        ->where('badges.0.id', $badge->id)
        ->where('badges.0.developers_count', 0));
});
