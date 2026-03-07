<?php

namespace App\Console\Commands;

use App\Models\Developer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PostDeveloperSpotlightToLinkedInCommand extends Command
{
    protected $signature = 'linkedin:post-developer-spotlight
                            {--dry-run : Show the message without posting}';

    protected $description = 'Post a developer spotlight (name + projects) to LinkedIn personal profile. Scheduled every 2 days.';

    public function handle(): int
    {
        $developer = Developer::query()
            ->available()
            ->whereHas('projects', fn ($q) => $q->visible())
            ->with(['projects' => fn ($q) => $q->visible()])
            ->inRandomOrder()
            ->first();

        if (! $developer) {
            $this->warn('No available developer with visible projects found.');

            return self::SUCCESS;
        }

        $profileUrl = route('developers.show', $developer->slug, true);
        $projectsList = $developer->projects
            ->map(fn ($p) => $p->link ? "{$p->title} {$p->link}" : $p->title)
            ->implode(' ');

        $message = $this->buildMessage($developer->name, $projectsList, $profileUrl);

        if ($this->option('dry-run')) {
            $this->info('Dry run — would post the following:');
            $this->line('');
            $this->line($message);
            $this->line('');

            return self::SUCCESS;
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
            'commentary' => trim($message),
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
            $postId = $response->header('x-restli-id');
            $this->info("Developer spotlight posted for {$developer->name}. Post ID: {$postId}");

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

    private function buildMessage(string $name, string $projectsList, string $profileUrl): string
    {
        $templates = [
            fn () => implode("\n", [
                'شفت مشاريع مبرمج بمنصة find-developer.com و عجبني و حبيت اشاركهن وياكم',
                "{$name}",
                $projectsList ?: '—',
                "البروفايل {$profileUrl}",
            ]),
            fn () => implode("\n", [
                'عجبني مشاريع مبرمج من find-developer.com و حبيت اشارككم',
                "{$name}",
                $projectsList ?: '—',
                $profileUrl,
            ]),
            fn () => implode("\n", [
                'صادفني مبرمج بمشاريع حلوة بمنصة find-developer.com',
                "{$name}",
                $projectsList ?: '—',
                "البروفايل {$profileUrl}",
            ]),
            fn () => implode("\n", [
                'لقيت مبرمج من find-developer.com و مشاريعه عجبتني',
                "{$name}",
                $projectsList ?: '—',
                $profileUrl,
            ]),
            fn () => implode("\n", [
                'شفت بروفايل مبرمج بمنصة find-developer.com و المشاريع حلوة',
                "{$name}",
                $projectsList ?: '—',
                "البروفايل {$profileUrl}",
            ]),
        ];

        return collect($templates)->random()();
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
