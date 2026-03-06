<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class LinkedinDebugPersonalCommand extends Command
{
    protected $signature = 'linkedin:debug-personal';

    protected $description = 'Diagnose LinkedIn personal posting: test /me, verify person ID, and attempt post with full error output.';

    public function handle(): int
    {
        $accessToken = config('linkedin.personal.access_token');
        if (empty($accessToken)) {
            $this->error('LINKEDIN_PERSONAL_ACCESS_TOKEN is not set in .env.');

            return self::FAILURE;
        }

        $personId = config('linkedin.personal.person_id');

        $this->info('1. Testing GET /v2/me (person ID)...');
        $meResponse = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
            'X-Restli-Protocol-Version' => '2.0.0',
        ])->get('https://api.linkedin.com/v2/me');

        if ($meResponse->successful()) {
            $data = $meResponse->json();
            $personId = $data['id'] ?? $personId;
            $this->info("   ✓ Success. Person ID: {$personId}");
            $this->line('   Response: '.json_encode($data, JSON_PRETTY_PRINT));
        } else {
            $this->error("   ✗ Failed: {$meResponse->status()}");
            $this->line('   Body: '.$meResponse->body());

            $this->info('   Trying OpenID userinfo as fallback...');
            $userinfoResponse = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
            ])->get('https://api.linkedin.com/v2/userinfo');

            if ($userinfoResponse->successful()) {
                $data = $userinfoResponse->json();
                $sub = $data['sub'] ?? null;
                $this->info("   ✓ Userinfo success. sub (person ID): {$sub}");
                $this->line('   Response: '.json_encode($data, JSON_PRETTY_PRINT));
                if (! empty($sub)) {
                    $personId = $sub;
                }
            } else {
                $this->error("   ✗ Userinfo failed: {$userinfoResponse->status()}");
                $this->line('   Body: '.$userinfoResponse->body());
                $this->warn('   Add r_liteprofile scope and set LINKEDIN_PERSONAL_PERSON_ID to the correct ID.');
            }
        }

        $personId = $personId ?: ($meResponse->json()['id'] ?? null);
        if (empty($personId)) {
            $this->error('Cannot proceed without person ID.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('2. Attempting POST /rest/posts...');
        $payload = [
            'author' => "urn:li:person:{$personId}",
            'commentary' => 'Debug test post - safe to delete',
            'visibility' => 'PUBLIC',
            'distribution' => [
                'feedDistribution' => 'MAIN_FEED',
                'targetEntities' => [],
                'thirdPartyDistributionChannels' => [],
            ],
            'lifecycleState' => 'PUBLISHED',
            'isReshareDisabledByAuthor' => false,
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
            'X-Restli-Protocol-Version' => '2.0.0',
            'LinkedIn-Version' => config('linkedin.api_version', '202508'),
            'Content-Type' => 'application/json',
        ])->post('https://api.linkedin.com/rest/posts', $payload);

        if ($response->successful()) {
            $this->info('   ✓ Post created successfully!');
            $this->line('   Post ID: '.$response->header('x-restli-id'));

            return self::SUCCESS;
        }

        $this->error("   ✗ Failed: {$response->status()}");
        $this->line('   Body: '.$response->body());
        $this->line('   Headers: '.json_encode($response->headers(), JSON_PRETTY_PRINT));
        $this->line('   Payload author: urn:li:person:'.$personId);

        return self::FAILURE;
    }
}
