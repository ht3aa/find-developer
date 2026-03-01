<?php

namespace App\Console\Commands;

use App\Mail\NewsletterDevelopersMail;
use App\Models\Developer;
use App\Models\Newsletter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendNewsletterDevelopersCommand extends Command
{
    protected $signature = 'newsletter:send-developers';

    protected $description = 'Select 10 available developers (with at least 2 badges, companies, projects, CV, and skills) by experience and email them to newsletter subscribers.';

    public function handle(): int
    {
        $belowTwo = $this->queryDevelopers(0, 1)->inRandomOrder()->limit(4)->get();
        $twoToFour = $this->queryDevelopers(2, 4)->inRandomOrder()->limit(4)->get();
        $aboveFour = $this->queryDevelopers(5, null)->inRandomOrder()->limit(2)->get();

        $developers = $belowTwo->concat($twoToFour)->concat($aboveFour);

        if ($developers->count() < 10) {
            $this->warn(sprintf(
                'Only %d developer(s) found (available, with at least 2 badges, companies, projects, CV, and skills). Need 10 (4 below 2y, 4 between 2–4y, 2 above 4y).',
                $developers->count()
            ));
        }

        if ($developers->isEmpty()) {
            $this->error('No developers match the criteria (available, 2+ badges, companies, projects, CV, skills). No emails sent.');

            return self::FAILURE;
        }

        $subscribers = Newsletter::all();
        if ($subscribers->isEmpty()) {
            $this->warn('No newsletter subscribers. No emails sent.');

            return self::SUCCESS;
        }

        $payload = $developers->sortByDesc('years_of_experience')->values()->map(fn (Developer $d) => [
            'name' => $d->name,
            'profile_url' => route('developers.show', $d->slug, true),
            'job_title' => $d->jobTitle?->name,
            'years_of_experience' => $d->years_of_experience,
            'recommended_by_us' => $d->recommended_by_us,
        ])->all();

        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber->email)->send(new NewsletterDevelopersMail($payload));
        }

        $this->info(sprintf('Sent developer spotlight to %d subscriber(s).', $subscribers->count()));

        return self::SUCCESS;
    }

    private function queryDevelopers(int $minYears, ?int $maxYears): \Illuminate\Database\Eloquent\Builder
    {
        $query = Developer::query()
            ->available()
            ->withCount('badges')
            ->having('badges_count', '>=', 2)
            ->whereHas('companies')
            ->whereHas('projects')
            ->whereHas('skills')
            ->whereNotNull('cv_path')
            ->where('cv_path', '!=', '')
            ->with(['jobTitle:id,name']);

        return $query->byExperience($minYears, $maxYears);
    }
}
