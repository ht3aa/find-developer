<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PostToLinkedInPersonalCommand extends Command
{
    protected $signature = 'linkedin:post-personal
                            {--text= : The post content to publish}
                            {--dry-run : Validate and show payload without posting}';

    protected $description = 'Create a text post on your personal LinkedIn profile (requires w_member_social scope).';

    public function handle(): int
    {
        $text = $this->option('text') ?? $this->ask('Enter the post content');

        if (empty(trim((string) $text))) {
            $this->error('Post content cannot be empty.');

            return self::FAILURE;
        }

        $accessToken = config('linkedin.personal.access_token');
        if (empty($accessToken)) {
            $this->error('LINKEDIN_PERSONAL_ACCESS_TOKEN is not set in .env.');

            return self::FAILURE;
        }

        $personId = $this->resolvePersonId($accessToken);
        if (empty($personId)) {
            $this->error('Could not determine person ID. Set LINKEDIN_PERSONAL_PERSON_ID in .env or ensure your token has r_liteprofile scope for auto-fetch.');

            return self::FAILURE;
        }

        $payload = [
            'author' => "urn:li:person:{$personId}",
            'commentary' => trim((string) $text),
            'visibility' => 'PUBLIC',
            'distribution' => [
                'feedDistribution' => 'MAIN_FEED',
                'targetEntities' => [],
                'thirdPartyDistributionChannels' => [],
            ],
            'lifecycleState' => 'PUBLISHED',
            'isReshareDisabledByAuthor' => false,
        ];

        if ($this->option('dry-run')) {
            $this->info('Dry run — would post the following to your personal profile:');
            $this->line('');
            $this->line($payload['commentary']);
            $this->line('');
            $this->info('Payload: '.json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
            'X-Restli-Protocol-Version' => '2.0.0',
            'LinkedIn-Version' => config('linkedin.api_version', '202508'),
            'Content-Type' => 'application/json',
        ])->post('https://api.linkedin.com/rest/posts', $payload);

        if ($response->successful()) {
            $postId = $response->header('x-restli-id');
            $this->info("Post published successfully to your personal profile. Post ID: {$postId}");

            return self::SUCCESS;
        }

        $this->error("LinkedIn API error: {$response->status()}");
        $this->line($response->body());

        if ($response->status() === 401) {
            $this->warn('Your access token may be expired. LinkedIn tokens expire after 60 days. Generate a new token via OAuth.');
        }

        if ($response->status() === 403) {
            $this->warn('Ensure your app has w_member_social scope for posting to personal profiles.');
        }

        return self::FAILURE;
    }

    private function resolvePersonId(string $accessToken): ?string
    {
        $personId = config('linkedin.personal.person_id');
        if (! empty($personId)) {
            return trim($personId);
        }

        $meResponse = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
            'X-Restli-Protocol-Version' => '2.0.0',
        ])->get('https://api.linkedin.com/v2/me');

        if ($meResponse->successful()) {
            $data = $meResponse->json();

            return $data['id'] ?? null;
        }

        $userinfoResponse = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
        ])->get('https://api.linkedin.com/v2/userinfo');

        if ($userinfoResponse->successful()) {
            $data = $userinfoResponse->json();

            return $data['sub'] ?? null;
        }

        return null;
    }
}
