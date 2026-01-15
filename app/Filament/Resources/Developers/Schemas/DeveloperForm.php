<?php

namespace App\Filament\Resources\Developers\Schemas;

use App\Enums\DeveloperStatus;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeveloperForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),

                        TextInput::make('location')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Professional Information')
                    ->schema([
                        Select::make('job_title_id')
                            ->relationship('jobTitle', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Textarea::make('description')
                                    ->rows(3),
                            ]),

                        TextInput::make('years_of_experience')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(50)
                            ->suffix('years')
                            ->required(),

                        TextInput::make('expected_salary_from')
                            ->label('Expected Salary From (IQD)')
                            ->numeric()
                            ->suffix('IQD')
                            ->step(1000)
                            ->minValue(0),

                        TextInput::make('expected_salary_to')
                            ->label('Expected Salary To (IQD)')
                            ->numeric()
                            ->suffix('IQD')
                            ->step(1000)
                            ->minValue(0),

                        Select::make('status')
                            ->options(DeveloperStatus::class)
                            ->default(DeveloperStatus::PENDING)
                            ->required(),

                        Toggle::make('is_available')
                            ->label('Available for hire')
                            ->default(true)
                            ->required(),

                        Textarea::make('bio')
                            ->rows(4)
                            ->columnSpanFull(),

                        Select::make('skills')
                            ->relationship('skills', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Links')
                    ->schema([
                        TextInput::make('portfolio_url')
                            ->url()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-globe-alt'),

                        TextInput::make('github_url')
                            ->url()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-code-bracket'),

                        TextInput::make('linkedin_url')
                            ->url()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-user-circle'),
                    ])
                    ->columns(3),
            ]);
    }
}
