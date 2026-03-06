<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');

Schedule::command('newsletter:send-developers')->weekly();

Schedule::command('developers:send-weekly-profile-views')->weekly();

Schedule::command('backup:run --only-db')->dailyAt('01:00');
Schedule::command('backup:clean')->dailyAt('02:00');
