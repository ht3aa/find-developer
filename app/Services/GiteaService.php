<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class GiteaService
{
    public function isConfigured(): bool
    {
        return filled(config('services.gitea.url')) && filled(config('services.gitea.token'));
    }

    protected function http(): PendingRequest
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Gitea is not configured. Set GITEA_URL and GITEA_TOKEN in your environment.');
        }

        $baseUrl = rtrim((string) config('services.gitea.url'), '/');
        $token = (string) config('services.gitea.token');

        return Http::baseUrl($baseUrl)
            ->withHeaders([
                'Authorization' => 'token '.$token,
            ])
            ->acceptJson()
            ->asJson();
    }

    /**
     * @return array<string, mixed>
     */
    public function createUser(
        string $username,
        string $email,
        string $password,
        ?string $fullName = null,
        bool $mustChangePassword = true,
    ): array {
        $payload = [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'must_change_password' => $mustChangePassword,
        ];

        if ($fullName !== null && $fullName !== '') {
            $payload['full_name'] = $fullName;
        }

        /** @var Response $response */
        $response = $this->http()->post('/api/v1/admin/users', $payload);

        return $this->decodeSuccessfulJson($response);
    }

    /**
     * Create a repository under a user (admin API).
     *
     * @return array<string, mixed>
     */
    public function createRepositoryForUser(
        string $giteaUsername,
        string $repositoryName,
        string $description = '',
        bool $private = true,
    ): array {
        $username = rawurlencode($giteaUsername);

        /** @var Response $response */
        $response = $this->http()->post("/api/v1/admin/users/{$username}/repos", [
            'name' => $repositoryName,
            'description' => $description,
            'private' => $private,
            'auto_init' => true,
        ]);

        return $this->decodeSuccessfulJson($response);
    }

    /**
     * Add a collaborator to a repository.
     */
    public function addCollaborator(
        string $owner,
        string $repo,
        string $collaboratorUsername,
        string $permission = 'write',
    ): void {
        $ownerEnc = rawurlencode($owner);
        $repoEnc = rawurlencode($repo);
        $userEnc = rawurlencode($collaboratorUsername);

        /** @var Response $response */
        $response = $this->http()->put("/api/v1/repos/{$ownerEnc}/{$repoEnc}/collaborators/{$userEnc}", [
            'permission' => $permission,
        ]);

        if (! $response->successful()) {
            $this->throwFromResponse($response);
        }
    }

    /**
     * Try to resolve an existing Gitea login for the given email (used when createUser returns duplicate).
     */
    public function findLoginByEmail(string $email): ?string
    {
        /** @var Response $search */
        $search = $this->http()->get('/api/v1/users/search', [
            'q' => $email,
            'limit' => 20,
        ]);

        if ($search->successful()) {
            foreach ($search->json('data') ?? [] as $row) {
                if (is_array($row) && ($row['email'] ?? '') === $email) {
                    return isset($row['login']) ? (string) $row['login'] : null;
                }
            }
        }

        $login = $this->suggestedUsernameFromEmail($email);
        $loginEnc = rawurlencode($login);

        /** @var Response $byLogin */
        $byLogin = $this->http()->get("/api/v1/users/{$loginEnc}");

        if ($byLogin->successful()) {
            $json = $byLogin->json();
            if (is_array($json) && ($json['email'] ?? '') === $email) {
                return isset($json['login']) ? (string) $json['login'] : null;
            }
        }

        return null;
    }

    public function suggestedUsernameFromEmail(string $email): string
    {
        $local = Str::before($email, '@');
        $sanitized = preg_replace('/[^a-zA-Z0-9._-]/', '', $local) ?? '';

        if ($sanitized === '') {
            $sanitized = 'user'.substr(sha1($email), 0, 8);
        }

        return Str::limit($sanitized, 35, '');
    }

    /**
     * @return array<string, mixed>
     */
    protected function decodeSuccessfulJson(Response $response): array
    {
        if (! $response->successful()) {
            $this->throwFromResponse($response);
        }

        /** @var array<string, mixed> */
        return $response->json();
    }

    protected function throwFromResponse(Response $response): never
    {
        $json = $response->json();
        $message = is_array($json) && isset($json['message'])
            ? (string) $json['message']
            : $response->body();

        throw new RuntimeException($message !== '' ? $message : 'Gitea API request failed.');
    }
}
