<?php

namespace App\Filament\Resources\Hackathons\Resources\HackathonTeams\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HackathonTeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('logo')
                    ->label('Team logo')
                    ->image()
                    ->disk('s3')
                    ->directory('hackathon-teams')
                    ->visibility('public')
                    ->storeFiles(false)
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                    ->nullable(),
            ]);
    }
}
