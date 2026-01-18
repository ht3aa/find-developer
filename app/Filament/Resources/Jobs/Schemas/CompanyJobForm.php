<?php

namespace App\Filament\Resources\Jobs\Schemas;

use App\Enums\JobStatus;
use App\Enums\SalaryCurrency;
use App\Enums\WorldGovernorate;
use App\Filament\Customs\ExpectedSalaryFromField;
use App\Filament\Customs\ExpectedSalaryToField;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CompanyJobForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Job Information')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->afterStateUpdatedJs(<<<'JS'
                            $set('slug', ($state).replaceAll(' ', '-').toLowerCase());
                            JS),

                        TextInput::make('slug')
                            ->label('URL Slug')
                            ->maxLength(255)
                            ->unique('company_jobs', 'slug', ignoreRecord: true)
                            ->readonly()
                            ->helperText('Auto-generated from title'),

                        Select::make('job_title_id')
                            ->label('Job Title')
                            ->relationship('jobTitle', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Textarea::make('description')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),

                        Textarea::make('requirements')
                            ->label('Requirements')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('List the requirements for this position...'),
                    ])
                    ->columns(2),

                Section::make('Company Information')
                    ->schema([
                        TextInput::make('company_name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        TextInput::make('contact_link')
                            ->url()
                            ->maxLength(255)
                            ->label('Contact Link')
                            ->placeholder('https://...'),

                        Select::make('location')
                            ->options(WorldGovernorate::class)
                            ->searchable(),
                    ])
                    ->columns(2),

                Section::make('Salary & Status')
                    ->schema([
                        ExpectedSalaryFromField::makeWithoutLt('salary_from')
                            ->lt('salary_to'),
                        ExpectedSalaryToField::makeWithoutGt('salary_to')
                            ->gt('salary_from'),

                        Select::make('salary_currency')
                            ->label('Salary Currency')
                            ->options(SalaryCurrency::class)
                            ->searchable()
                            ->default(SalaryCurrency::IQD),

                        Select::make('status')
                            ->options(JobStatus::class)
                            ->default(JobStatus::PENDING)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
