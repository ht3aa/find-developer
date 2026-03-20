<?php

namespace App\Console\Commands;

use App\Models\Developer;
use Illuminate\Console\Command;

class ListDeveloperEmailsCommand extends Command
{
    protected $signature = 'developers:list-emails';

    protected $description = 'Print all developer profile emails, comma-separated (includes non-approved records).';

    public function handle(): int
    {
        $emails = Developer::withoutGlobalScopes()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->orderBy('id')
            ->pluck('email')
            ->unique()
            ->values()
            ->all();

        $this->line(implode(',', $emails));

        return self::SUCCESS;
    }
}
