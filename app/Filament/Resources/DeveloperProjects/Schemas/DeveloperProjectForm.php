<?php

namespace App\Filament\Resources\DeveloperProjects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeveloperProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Project Information')
                    ->description('Add or edit project details')
                    ->schema([
                        Select::make('developer_id')
                            ->label('Developer')
                            ->relationship('developer', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull(),

                        TextInput::make('link')
                            ->label('Project Link')
                            ->url()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-link')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
