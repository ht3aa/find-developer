<?php

namespace App\Filament\Resources\DeveloperOffers\Schemas;

use App\Enums\AvailabilityType;
use App\Enums\OfferStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeveloperOfferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Offer Details')
                    ->schema([
                        Select::make('developer_id')
                            ->label('Developer')
                            ->relationship('developer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn ($operation) => $operation === 'edit')
                            ->dehydrated(fn ($operation) => $operation !== 'edit'),

                        Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn ($operation) => $operation === 'edit')
                            ->dehydrated(fn ($operation) => $operation !== 'edit'),

                        TextInput::make('company_name')
                            ->label('Company Name')
                            ->required()
                            ->maxLength(255),

                        Select::make('job_title_id')
                            ->label('Position')
                            ->relationship('jobTitle', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('salary_range')
                            ->label('Salary Range')
                            ->maxLength(255),

                        Select::make('work_type')
                            ->label('Work Type')
                            ->options(AvailabilityType::class),

                        TextInput::make('contact_email')
                            ->label('Contact Email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Select::make('status')
                            ->label('Status')
                            ->options(OfferStatus::class)
                            ->required()
                            ->default(OfferStatus::PENDING),

                        Textarea::make('message')
                            ->label('Message')
                            ->required()
                            ->rows(6)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
