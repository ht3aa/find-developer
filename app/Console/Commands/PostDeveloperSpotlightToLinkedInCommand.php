<?php

namespace App\Console\Commands;

use App\Models\Developer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

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

        $exitCode = Artisan::call('linkedin:post-personal', [
            '--text' => $message,
        ]);

        if ($exitCode === 0) {
            $this->info("Developer spotlight posted for {$developer->name}.");

            return self::SUCCESS;
        }

        $this->error('Failed to post to LinkedIn. See output above.');

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
}
