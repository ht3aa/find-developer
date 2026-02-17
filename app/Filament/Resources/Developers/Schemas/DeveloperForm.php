<?php

namespace App\Filament\Resources\Developers\Schemas;

use App\Enums\AvailabilityType;
use App\Enums\Currency;
use App\Enums\DeveloperStatus;
use App\Enums\SubscriptionPlan;
use App\Enums\WorldGovernorate;
use App\Filament\Customs\ExpectedSalaryFromField;
use App\Filament\Customs\ExpectedSalaryToField;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                        Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        TextInput::make('name')
                            ->required()
                            ->afterStateUpdatedJs(<<<'JS'
                            $set('slug', ($state).replaceAll(' ', '-').toLowerCase());
                            JS)
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label('URL Slug')
                            ->maxLength(255)
                            ->unique('developers', 'slug', ignoreRecord: true)
                            ->readonly()
                            ->helperText('Leave empty to auto-generate from name'),

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
                            ->default(DeveloperStatus::PENDING)
                            ->required(),

                        Select::make('subscription_plan')
                            ->label('Subscription Plan')
                            ->options(SubscriptionPlan::class)
                            ->default(SubscriptionPlan::FREE)
                            ->required(),

                        Toggle::make('is_available')
                            ->label('Available for hire')
                            ->default(true)
                            ->required(),

                        Toggle::make('special_needs')
                            ->label('Special Needs Developer')
                            ->default(false),

                        Select::make('availability_type')
                            ->label('Availability Type')
                            ->options(AvailabilityType::class)
                            ->multiple()
                            ->searchable()
                            ->nullable()
                            ->helperText('Select your preferred work arrangement(s)'),

                        Toggle::make('recommended_by_us')
                            ->label('Recommended By Us')
                            ->default(false)
                            ->helperText('Mark this developer as recommended by us'),

                        Textarea::make('bio')
                            ->rows(4)
                            ->columnSpanFull(),

                        Select::make('skills')
                            ->relationship('skills', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),

                        Select::make('badges')
                            ->relationship('badges', 'name', fn($query) => $query->where('is_active', true))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull()
                            ->helperText('Select badges earned by this developer'),
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

                Section::make('CV / Resume')
                    ->schema([
                        FileUpload::make('cv_path')
                            ->label('CV / Resume (PDF only)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('developer-cvs')
                            ->maxSize(1024 * 5)
                            ->helperText('Upload a PDF version of the developer\'s CV. Max 5MB.'),
                    ]),
            ]);
    }
}
