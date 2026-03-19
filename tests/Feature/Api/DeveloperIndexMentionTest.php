<?php

use App\Models\Developer;

it('includes profile_url in api developer listings for message mentions', function () {
    $developer = Developer::factory()->create([
        'name' => 'Mention Test Developer',
    ]);

    $response = $this->getJson('/api/developers?filter[search]=Mention+Test+Developer&per_page=10');

    $response->assertSuccessful();
    $profileUrls = collect($response->json('data'))->pluck('profile_url')->all();
    expect($profileUrls)->toContain(url('/developers/'.$developer->slug));
});

it('filters developers by search for the mention picker', function () {
    Developer::factory()->create(['name' => 'UniqueZebraMentionName']);
    Developer::factory()->create(['name' => 'Someone Else']);

    $response = $this->getJson('/api/developers?filter[search]=UniqueZebraMentionName&per_page=10');

    $response->assertSuccessful();
    $names = collect($response->json('data'))->pluck('name')->all();
    expect($names)->toContain('UniqueZebraMentionName')
        ->and($names)->not->toContain('Someone Else');
});
