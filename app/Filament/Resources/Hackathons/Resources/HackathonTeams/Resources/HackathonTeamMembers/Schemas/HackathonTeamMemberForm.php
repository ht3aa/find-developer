<?php

namespace App\Filament\Resources\Hackathons\Resources\HackathonTeams\Resources\HackathonTeamMembers\Schemas;

use App\Enums\HackathonMemberPosition;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class HackathonTeamMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('developer_id')
                    ->label('Developer')
                    ->relationship('developer', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('position')
                    ->options(collect(HackathonMemberPosition::cases())->mapWithKeys(
                        fn (HackathonMemberPosition $position): array => [$position->value => $position->label()]
                    ))
                    ->required(),
            ]);
    }
}
