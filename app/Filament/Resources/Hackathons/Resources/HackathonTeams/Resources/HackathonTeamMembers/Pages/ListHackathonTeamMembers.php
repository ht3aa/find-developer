<?php

namespace App\Filament\Resources\Hackathons\Resources\HackathonTeams\Resources\HackathonTeamMembers\Pages;

use App\Filament\Resources\Hackathons\Resources\HackathonTeams\Resources\HackathonTeamMembers\HackathonTeamMemberResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHackathonTeamMembers extends ListRecords
{
    protected static string $resource = HackathonTeamMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
