<?php

use App\Services\GiteaService;
use Illuminate\Support\Facades\Http;

it('reports configured when url and token are set', function () {
    config([
        'services.gitea.url' => 'https://git.example.com',
        'services.gitea.token' => 'secret',
    ]);

    expect(app(GiteaService::class)->isConfigured())->toBeTrue();
});

it('reports not configured when token is missing', function () {
    config([
        'services.gitea.url' => 'https://git.example.com',
        'services.gitea.token' => null,
    ]);

    expect(app(GiteaService::class)->isConfigured())->toBeFalse();
});

it('creates user via gitea admin api', function () {
    config([
        'services.gitea.url' => 'https://git.example.com',
        'services.gitea.token' => 't0ken',
    ]);

    Http::fake([
        'https://git.example.com/api/v1/admin/users' => Http::response([
            'id' => 1,
            'login' => 'jane',
            'email' => 'jane@example.com',
        ], 201),
    ]);

    $result = app(GiteaService::class)->createUser(
        username: 'jane',
        email: 'jane@example.com',
        password: 'secret-password',
        fullName: 'Jane Doe',
    );

    expect($result['login'])->toBe('jane');

    Http::assertSent(function ($request) {
        $data = $request->data();

        return $request->url() === 'https://git.example.com/api/v1/admin/users'
            && $request->hasHeader('Authorization', 'token t0ken')
            && $data['username'] === 'jane'
            && $data['email'] === 'jane@example.com'
            && $data['password'] === 'secret-password'
            && $data['full_name'] === 'Jane Doe'
            && $data['must_change_password'] === true;
    });
});

it('throws on gitea api error', function () {
    config([
        'services.gitea.url' => 'https://git.example.com',
        'services.gitea.token' => 't0ken',
    ]);

    Http::fake([
        'https://git.example.com/api/v1/admin/users' => Http::response([
            'message' => 'User already exists',
        ], 422),
    ]);

    app(GiteaService::class)->createUser(
        username: 'jane',
        email: 'jane@example.com',
        password: 'secret-password',
    );
})->throws(RuntimeException::class, 'User already exists');

it('suggests username from email local part', function () {
    $service = app(GiteaService::class);

    expect($service->suggestedUsernameFromEmail('john.doe@example.com'))->toBe('john.doe');
});

it('suggests fallback username when local part is empty', function () {
    $service = app(GiteaService::class);

    expect($service->suggestedUsernameFromEmail('@example.com'))->toStartWith('user');
});

it('creates a private repository for a user via admin api', function () {
    config([
        'services.gitea.url' => 'https://git.example.com',
        'services.gitea.token' => 't0ken',
    ]);

    Http::fake([
        'https://git.example.com/api/v1/admin/users/poster/repos' => Http::response([
            'name' => 'remote-work-1',
            'full_name' => 'poster/remote-work-1',
        ], 201),
    ]);

    $result = app(GiteaService::class)->createRepositoryForUser(
        'poster',
        'remote-work-1',
        'A project',
        true,
    );

    expect($result['name'])->toBe('remote-work-1');

    Http::assertSent(function ($request) {
        $data = $request->data();

        return $request->url() === 'https://git.example.com/api/v1/admin/users/poster/repos'
            && $data['name'] === 'remote-work-1'
            && $data['private'] === true;
    });
});

it('adds a collaborator to a repository', function () {
    config([
        'services.gitea.url' => 'https://git.example.com',
        'services.gitea.token' => 't0ken',
    ]);

    Http::fake([
        'https://git.example.com/api/v1/repos/owner/repo/collaborators/devuser' => Http::response([], 204),
    ]);

    app(GiteaService::class)->addCollaborator('owner', 'repo', 'devuser', 'write');

    Http::assertSent(function ($request) {
        $data = $request->data();

        return $request->url() === 'https://git.example.com/api/v1/repos/owner/repo/collaborators/devuser'
            && ($data['permission'] ?? null) === 'write';
    });
});
