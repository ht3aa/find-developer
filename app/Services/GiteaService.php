<?php

namespace App\Services;

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
        if (! $this->isConfigured()) {
            throw new RuntimeException('Gitea is not configured. Set GITEA_URL and GITEA_TOKEN in your environment.');
        }

        $baseUrl = rtrim((string) config('services.gitea.url'), '/');
        $token = (string) config('services.gitea.token');

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
        $response = Http::baseUrl($baseUrl)
            ->withHeaders([
                'Authorization' => 'token '.$token,
            ])
            ->acceptJson()
            ->asJson()
            ->post('/api/v1/admin/users', $payload);

        if (! $response->successful()) {
            $json = $response->json();
            $message = is_array($json) && isset($json['message'])
                ? (string) $json['message']
                : $response->body();

            throw new RuntimeException($message !== '' ? $message : 'Gitea API request failed.');
        }

        /** @var array<string, mixed> */
        return $response->json();
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
}
