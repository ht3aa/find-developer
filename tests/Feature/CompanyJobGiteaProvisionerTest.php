<?php

use App\Models\CompanyJob;
use App\Models\User;
use App\Services\CompanyJobGiteaProvisioner;
use Illuminate\Support\Facades\Http;

it('creates a gitea repository named from the job slug', function () {
    config([
        'services.gitea.url' => 'https://git.example.com',
        'services.gitea.token' => 'token',
    ]);

    $owner = User::factory()->create([
        'gitea_username' => 'owner',
    ]);

    $expectedName = 'senior-laravel-dev-a1b2c3';

    $job = CompanyJob::factory()->for($owner)->create([
        'slug' => $expectedName,
        'gitea_provisioned_at' => null,
    ]);

    Http::fake([
        'https://git.example.com/api/v1/admin/users/owner/repos' => Http::response([
            'name' => $expectedName,
            'full_name' => 'owner/'.$expectedName,
        ], 201),
    ]);

    app(CompanyJobGiteaProvisioner::class)->provisionRepositoryForApprovedJob($job);

    Http::assertSent(function ($request) use ($expectedName) {
        return $request->url() === 'https://git.example.com/api/v1/admin/users/owner/repos'
            && $request->data()['name'] === $expectedName;
    });

    $job->refresh();

    expect($job->gitea_repo_name)->toBe($expectedName);
});

it('falls back when the job slug is empty', function () {
    config([
        'services.gitea.url' => 'https://git.example.com',
        'services.gitea.token' => 'token',
    ]);

    $owner = User::factory()->create([
        'gitea_username' => 'owner',
    ]);

    $job = CompanyJob::factory()->for($owner)->create([
        'gitea_provisioned_at' => null,
    ]);

    $job->forceFill(['slug' => ''])->save();

    $expectedName = 'remote-work-'.$job->id;

    Http::fake([
        'https://git.example.com/api/v1/admin/users/owner/repos' => Http::response([
            'name' => $expectedName,
            'full_name' => 'owner/'.$expectedName,
        ], 201),
    ]);

    app(CompanyJobGiteaProvisioner::class)->provisionRepositoryForApprovedJob($job);

    Http::assertSent(function ($request) use ($expectedName) {
        return $request->data()['name'] === $expectedName;
    });

    $job->refresh();

    expect($job->gitea_repo_name)->toBe($expectedName);
});
