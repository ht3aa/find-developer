<?php

namespace App\Filament\Resources\Hackathons\Pages;

use App\Filament\Resources\Hackathons\HackathonResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHackathons extends ListRecords
{
    protected static string $resource = HackathonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
