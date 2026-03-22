<?php

namespace App\Filament\Resources\Hackathons\Resources\HackathonTeams\Pages;

use App\Filament\Resources\Hackathons\Resources\HackathonTeams\HackathonTeamResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHackathonTeams extends ListRecords
{
    protected static string $resource = HackathonTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
