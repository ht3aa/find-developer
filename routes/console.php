<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');

Schedule::command('newsletter:send-developers')->weekly();

Schedule::command('developers:send-weekly-profile-views')->weekly();

// Schedule::command('linkedin:post-developer-spotlight')->dailyAt('22:00');

// Schedule::command('linkedin:post-developer-profile')->cron('0 20 */2 * *');

Schedule::command('messages:notify-unread')->dailyAt('09:00');

Schedule::command('backup:run --only-db')->twiceDaily(0, 12);
Schedule::command('backup:clean')->twiceDaily(1, 13);
