<?php

namespace App\Filament\Resources\CompanyJobs\Schemas;

use App\Enums\Currency;
use App\Enums\JobStatus;
use App\Enums\WorldGovernorate;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CompanyJobForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->default(null),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('company_name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('contact_link')
                    ->default(null),
                Select::make('location')
                    ->options(WorldGovernorate::class)
                    ->default(null),
                Select::make('job_title_id')
                    ->relationship('jobTitle', 'name')
                    ->nullable(),
                TextInput::make('salary_from')
                    ->numeric()
                    ->default(null),
                TextInput::make('salary_to')
                    ->numeric()
                    ->default(null),
                Select::make('salary_currency')
                    ->options(Currency::class)
                    ->default('IQD')
                    ->required(),
                Textarea::make('requirements')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(JobStatus::class)
                    ->default('pending')
                    ->required(),
                Toggle::make('first_payment_qi_confirmed')
                    ->default(false),
                TextInput::make('gitea_owner')
                    ->default(null),
                TextInput::make('gitea_repo_name')
                    ->default(null),
                DateTimePicker::make('gitea_provisioned_at'),
            ]);
    }
}
