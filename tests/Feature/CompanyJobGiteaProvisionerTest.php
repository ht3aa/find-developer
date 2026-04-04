<?php

use App\Models\CompanyJob;
use App\Models\User;
use App\Notifications\GiteaAccountCredentialsNotification;
use App\Services\CompanyJobGiteaProvisioner;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

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

it('sends mailtrap credentials when a new gitea user is created', function () {
    Notification::fake();

    config([
        'services.gitea.url' => 'https://git.example.com',
        'services.gitea.token' => 'token',
    ]);

    $user = User::factory()->create([
        'gitea_username' => null,
        'email' => 'newgitea@example.com',
    ]);

    Http::fake([
        'https://git.example.com/api/v1/admin/users' => Http::response([
            'login' => 'newgitea',
            'username' => 'newgitea',
        ], 201),
    ]);

    app(CompanyJobGiteaProvisioner::class)->ensureUserHasGiteaAccount($user);

    $user->refresh();

    expect($user->gitea_username)->toBe('newgitea');

    Notification::assertSentTo(
        $user,
        GiteaAccountCredentialsNotification::class,
        function (GiteaAccountCredentialsNotification $notification): bool {
            return $notification->giteaUsername === 'newgitea'
                && $notification->temporaryPassword !== '';
        }
    );
});

it('does not send credentials when the user already has a gitea username', function () {
    Notification::fake();

    config([
        'services.gitea.url' => 'https://git.example.com',
        'services.gitea.token' => 'token',
    ]);

    $user = User::factory()->create([
        'gitea_username' => 'existing',
    ]);

    app(CompanyJobGiteaProvisioner::class)->ensureUserHasGiteaAccount($user);

    Notification::assertNothingSent();
});
