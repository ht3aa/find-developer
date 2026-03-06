<?php

namespace App\Console\Commands;

use App\Models\Developer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PostDeveloperProfileToLinkedInCommand extends Command
{
    protected $signature = 'linkedin:post-developer-profile
                            {--dry-run : Show the message without posting}';

    protected $description = 'Post a full developer profile spotlight to LinkedIn. Uses developers with 2+ badges, companies, projects, skills, and CV.';

    public function handle(): int
    {
        $developer = Developer::query()
            ->available()
            ->withCount('badges')
            ->having('badges_count', '>=', 2)
            ->whereHas('companies')
            ->whereHas('projects')
            ->whereHas('skills')
            ->whereNotNull('cv_path')
            ->where('cv_path', '!=', '')
            ->with([
                'jobTitle:id,name',
                'projects' => fn ($q) => $q->visible(),
                'companies' => fn ($q) => $q->visible(),
                'skills',
            ])
            ->inRandomOrder()
            ->first();

        if (! $developer) {
            $this->warn('No developer found matching the criteria.');

            return self::SUCCESS;
        }

        $profileUrl = route('developers.show', $developer->slug, true);
        $projectsList = $developer->projects
            ->map(fn ($p) => $p->link ? "{$p->title} {$p->link}" : $p->title)
            ->implode(' ');
        $skillsList = $developer->skills->pluck('name')->implode(' ');
        $companiesList = $developer->companies->pluck('company_name')->implode(' ');
        $experience = $developer->years_of_experience.' سنوات';
        if ($developer->jobTitle?->name) {
            $experience .= ' '.$developer->jobTitle->name;
        }
        $cvUrl = $developer->cv_path_url ?? $profileUrl;

        $message = $this->buildMessage($developer->name, $projectsList, $skillsList, $companiesList, $experience, $cvUrl, $profileUrl);

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
            $this->info("Developer profile posted for {$developer->name}.");

            return self::SUCCESS;
        }

        $this->error('Failed to post to LinkedIn. See output above.');

        return self::FAILURE;
    }

    private function buildMessage(string $name, string $projectsList, string $skillsList, string $companiesList, string $experience, string $cvUrl, string $profileUrl): string
    {
        $templates = [
            fn () => implode("\n", [
                'عجبني البروفايل لمبرمج بمنصة https://find-developer.com و حبيت اشيره وياكم',
                "اسم المبرمج {$name}",
                'مشاريع '.($projectsList ?: '—'),
                "خبرته {$experience}",
                'مهاراته '.($skillsList ?: '—'),
                'شركات '.($companiesList ?: '—'),
                "السيفي {$cvUrl}",
                "البروفايل {$profileUrl}",
            ]),
            fn () => implode("\n", [
                'شفت بروفايل حلو لمبرمج بمنصة find-developer.com و حبيت اشارككم',
                "{$name}",
                "خبرة {$experience}",
                'مشاريع '.($projectsList ?: '—'),
                'مهارات '.($skillsList ?: '—'),
                'شركات '.($companiesList ?: '—'),
                "السيفي {$cvUrl}",
                $profileUrl,
            ]),
            fn () => implode("\n", [
                'صادفني بروفايل لمبرمج من find-developer.com و عجبني',
                $name,
                $experience,
                $projectsList ?: '—',
                $skillsList ?: '—',
                $companiesList ?: '—',
                $cvUrl,
                $profileUrl,
            ]),
            fn () => implode("\n", [
                'عجبني مبرمج من find-developer.com و حبيت اشارككم',
                "{$name} {$experience}",
                'مشاريع '.($projectsList ?: '—'),
                'مهارات '.($skillsList ?: '—'),
                'شركات '.($companiesList ?: '—'),
                "{$cvUrl} {$profileUrl}",
            ]),
            fn () => implode("\n", [
                'لقيت مبرمج من find-developer.com و حبيت اشارككم بروفايله',
                "{$name}",
                $experience,
                $projectsList ?: '—',
                $skillsList ?: '—',
                $companiesList ?: '—',
                $cvUrl,
                "البروفايل {$profileUrl}",
            ]),
        ];

        return collect($templates)->random()();
    }
}
