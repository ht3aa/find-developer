<?php

use App\Filament\Resources\CompanyJobs\CompanyJobResource;
use App\Filament\Resources\CompanyJobs\RelationManagers\ApplicationsRelationManager;
use App\Models\CompanyJob;
use App\Models\User;

it('registers the participants relation manager on remote work posts', function () {
    expect(CompanyJobResource::getRelations())->toContain(ApplicationsRelationManager::class);
});

it('generates edit URLs using the company job slug as the record route key', function () {
    $owner = User::factory()->create();
    $job = CompanyJob::factory()->for($owner)->create([
        'slug' => 'acme-remote-role-xyz',
    ]);

    $url = CompanyJobResource::getUrl('edit', ['record' => $job], isAbsolute: false);

    expect($url)->toContain('acme-remote-role-xyz');
    expect($url)->toContain('/edit');
});
