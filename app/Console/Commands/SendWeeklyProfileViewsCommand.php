<?php

namespace App\Console\Commands;

use App\Mail\WeeklyProfileViewsMail;
use App\Models\Developer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWeeklyProfileViewsCommand extends Command
{
    protected $signature = 'developers:send-weekly-profile-views';

    protected $description = 'Email each developer their profile view count from the past week and encourage them to update their profile.';

    public function handle(): int
    {
        $since = now()->subWeek();

        $developers = Developer::query()
            ->withoutGlobalScopes()
            ->withCount(['profileViews as weekly_views_count' => fn ($q) => $q->where('created_at', '>=', $since)])
            ->get();

        if ($developers->isEmpty()) {
            $this->warn('No developers found. No emails sent.');

            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($developers as $developer) {
            $viewCount = $developer->weekly_views_count ?? 0;
            Mail::to($developer->email)->send(new WeeklyProfileViewsMail(
                developerName: $developer->name,
                viewCount: $viewCount,
                profileUrl: route('dashboard.developer-profile.index', [], true)
            ));
            $sent++;
        }

        $this->info(sprintf('Sent weekly profile views email to %d developer(s).', $sent));

        return self::SUCCESS;
    }
}
