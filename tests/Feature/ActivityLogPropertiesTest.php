<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;

uses(RefreshDatabase::class);

it('merges spatie v5 attribute_changes into activity log properties json', function () {
    $superEmail = 'super@example.com';
    config(['app.super_admin_emails' => $superEmail]);
    $user = User::factory()->create(['email' => $superEmail]);
    $this->actingAs($user);

    $activity = Activity::query()->create([
        'log_name' => 'default',
        'description' => 'updated',
        'subject_type' => null,
        'subject_id' => null,
        'causer_type' => null,
        'causer_id' => null,
        'event' => 'updated',
        'attribute_changes' => [
            'attributes' => ['title' => 'After'],
            'old' => ['title' => 'Before'],
        ],
        'properties' => null,
    ]);

    $response = $this->getJson(route('dashboard.activity-log.properties', ['id' => $activity->id]));

    $response->assertOk();
    expect($response->json('properties.attributes.title'))->toBe('After');
    expect($response->json('properties.old.title'))->toBe('Before');
});

it('merges custom properties with attribute_changes with changes winning overlapping keys', function () {
    $superEmail = 'super@example.com';
    config(['app.super_admin_emails' => $superEmail]);
    $user = User::factory()->create(['email' => $superEmail]);
    $this->actingAs($user);

    $activity = Activity::query()->create([
        'log_name' => 'default',
        'description' => 'updated',
        'subject_type' => null,
        'subject_id' => null,
        'causer_type' => null,
        'causer_id' => null,
        'event' => 'updated',
        'attribute_changes' => [
            'attributes' => ['title' => 'FromChanges'],
            'old' => ['title' => 'Old'],
        ],
        'properties' => [
            'note' => 'extra',
        ],
    ]);

    $response = $this->getJson(route('dashboard.activity-log.properties', ['id' => $activity->id]));

    $response->assertOk();
    expect($response->json('properties.note'))->toBe('extra');
    expect($response->json('properties.attributes.title'))->toBe('FromChanges');
    expect($response->json('properties.old.title'))->toBe('Old');
});
