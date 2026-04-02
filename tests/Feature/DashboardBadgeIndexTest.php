<?php

use App\Models\Badge;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;

it('renders the dashboard badges index for users who may view badges', function () {
    Permission::findOrCreate('ViewAny:Badge', 'web');

    $user = User::factory()->create();
    $user->givePermissionTo('ViewAny:Badge');

    Badge::factory()->create(['name' => 'Laravel Pro']);

    $this->actingAs($user)
        ->get(route('badges.index'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Badges/Index')
            ->has('badges', 1)
            ->where('badges.0.name', 'Laravel Pro')
            ->has('can', fn (Assert $can) => $can
                ->has('updateBadge')
                ->has('deleteBadge')));
});
