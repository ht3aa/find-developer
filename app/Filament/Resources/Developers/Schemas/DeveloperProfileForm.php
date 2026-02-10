<?php

namespace App\Filament\Resources\Developers\Schemas;

use App\Enums\AvailabilityType;
use App\Enums\Currency;
use App\Enums\DeveloperStatus;
use App\Enums\SubscriptionPlan;
use App\Enums\WorldGovernorate;
use App\Filament\Customs\ExpectedSalaryFromField;
use App\Filament\Customs\ExpectedSalaryToField;
use App\Models\Skill;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeveloperProfileForm
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

                        Select::make('location')
                            ->options(WorldGovernorate::class)
                            ->searchable(),
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

                        ExpectedSalaryFromField::make(),

                        ExpectedSalaryToField::make(),

                        Select::make('salary_currency')
                            ->label('Salary Currency')
                            ->options(Currency::class)
                            ->searchable()
                            ->default(Currency::IQD),

                        Select::make('status')
                            ->options(DeveloperStatus::class)
                            ->disabled()
                            ->dehydrated(),

                        Select::make('subscription_plan')
                            ->label('Subscription Plan')
                            ->options(SubscriptionPlan::class)
                            ->disabled()
                            ->dehydrated(),

                        Toggle::make('is_available')
                            ->label('Available for hire')
                            ->default(true)
                            ->required(),

                        Select::make('availability_type')
                            ->label('Availability Type')
                            ->options(AvailabilityType::class)
                            ->multiple()
                            ->searchable()
                            ->nullable()
                            ->helperText('Select your preferred work arrangement(s)'),

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


    private static function isDeveloperProfilePage($livewire): bool
    {
        return $livewire->getName() === 'app.filament.pages.developer-profile';
    }
}
