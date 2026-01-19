<?php

namespace App\Filament\Customs;

use Filament\Infolists\Components\KeyValueEntry;

class ActivityLogJson extends KeyValueEntry
{
    protected string $view = 'filament.components.activity-log-json';

    public function getOldValueLabel(): string
    {
        return 'القيمة القديمة';
    }
}
