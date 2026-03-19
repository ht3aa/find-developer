<?php

use App\Models\Developer;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;

test('guest does not receive salary fields on public developer profile', function () {
    $developer = Developer::factory()->create([
        'expected_salary_from' => 1_500_000,
        'expected_salary_to' => 2_000_000,
    ]);

    $this->get(route('developers.show', $developer->slug))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Developers/Show')
            ->where('developer.expected_salary_from', null)
            ->where('developer.expected_salary_to', null)
            ->where('developer.currency', null));
});

test('developer owner receives salary fields on public developer profile', function () {
    $developer = Developer::factory()->create([
        'expected_salary_from' => 1_500_000,
        'expected_salary_to' => 2_000_000,
    ]);
    $owner = $developer->user;
    $owner->markEmailAsVerified();

    $this->actingAs($owner)
        ->get(route('developers.show', $developer->slug))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Developers/Show')
            ->where('developer.expected_salary_from', 1_500_000)
            ->where('developer.expected_salary_to', 2_000_000)
            ->where('developer.currency', 'IQD'));
});

test('user with ViewCv permission receives salary fields on public developer profile', function () {
    Permission::findOrCreate('ViewCv:Developers', 'web');

    $developer = Developer::factory()->create([
        'expected_salary_from' => 900_000,
        'expected_salary_to' => 1_100_000,
    ]);

    $viewer = User::factory()->create();
    $viewer->markEmailAsVerified();
    $viewer->givePermissionTo('ViewCv:Developers');

    $this->actingAs($viewer)
        ->get(route('developers.show', $developer->slug))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Developers/Show')
            ->where('developer.expected_salary_from', 900_000)
            ->where('developer.expected_salary_to', 1_100_000)
            ->where('developer.currency', 'IQD'));
});
