<?php

namespace App\Filament\Resources\Developers\Schemas;

use App\Enums\AvailabilityType;
use App\Enums\Currency;
use App\Enums\DeveloperStatus;
use App\Enums\SubscriptionPlan;
use App\Enums\WorldGovernorate;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DeveloperForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->whereDoesntHave('developer')->orderBy('name'),
                    )
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(50),
                Select::make('job_title_id')
                    ->relationship(
                        name: 'jobTitle',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->where('is_active', true)->orderBy('name'),
                    )
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('years_of_experience')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(100),
                Textarea::make('bio')
                    ->maxLength(5000)
                    ->columnSpanFull(),
                TextInput::make('portfolio_url')
                    ->url()
                    ->maxLength(500),
                TextInput::make('github_url')
                    ->url()
                    ->maxLength(500),
                TextInput::make('linkedin_url')
                    ->url()
                    ->maxLength(500),
                TextInput::make('youtube_url')
                    ->maxLength(500),
                FileUpload::make('cv_path')
                    ->label('CV (PDF)')
                    ->disk('s3')
                    ->directory('cvs')
                    ->visibility('private')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(10240)
                    ->nullable(),
                Toggle::make('update_cv_automatic')
                    ->default(false),
                Select::make('location')
                    ->options(WorldGovernorate::class)
                    ->searchable()
                    ->nullable(),
                TextInput::make('expected_salary_from')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('expected_salary_to')
                    ->numeric()
                    ->minValue(0),
                Select::make('salary_currency')
                    ->options(Currency::class)
                    ->default(Currency::IQD),
                Toggle::make('is_available')
                    ->default(false),
                Toggle::make('special_needs')
                    ->default(false),
                CheckboxList::make('availability_type')
                    ->options(AvailabilityType::class)
                    ->columns(2)
                    ->columnSpanFull(),
                Toggle::make('recommended_by_us')
                    ->default(false),
                Select::make('status')
                    ->options(DeveloperStatus::class)
                    ->default(DeveloperStatus::PENDING)
                    ->required(),
                Select::make('subscription_plan')
                    ->options(SubscriptionPlan::class)
                    ->default(SubscriptionPlan::FREE)
                    ->required(),
                Select::make('skills')
                    ->relationship('skills', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Select::make('badges')
                    ->relationship('badges', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }
}
