<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PostToLinkedInCommand extends Command
{
    protected $signature = 'linkedin:post
                            {--text= : The post content to publish}
                            {--dry-run : Validate and show payload without posting}';

    protected $description = 'Create a text post on the configured LinkedIn company page.';

    public function handle(): int
    {
        $text = $this->option('text') ?? $this->ask('Enter the post content');

        if (empty(trim((string) $text))) {
            $this->error('Post content cannot be empty.');

            return self::FAILURE;
        }

        $accessToken = config('linkedin.access_token');
        $organizationId = config('linkedin.organization_id');

        if (empty($accessToken)) {
            $this->error('LINKEDIN_ACCESS_TOKEN is not set in .env.');

            return self::FAILURE;
        }

        if (empty($organizationId)) {
            $this->error('LINKEDIN_ORGANIZATION_ID is not set in .env.');

            return self::FAILURE;
        }

        $payload = [
            'author' => "urn:li:organization:{$organizationId}",
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
            $this->info('Dry run — would post the following:');
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
            $this->info("Post published successfully. Post ID: {$postId}");

            return self::SUCCESS;
        }

        $this->error("LinkedIn API error: {$response->status()}");
        $this->line($response->body());

        if ($response->status() === 401) {
            $this->warn('Your access token may be expired. LinkedIn tokens expire after 60 days. Generate a new token via OAuth.');
        }

        if ($response->status() === 403) {
            $this->warn('Ensure your app has w_organization_social scope and you are an admin of the company page.');
        }

        return self::FAILURE;
    }
}
