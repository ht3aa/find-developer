<?php

namespace App\Filament\Resources\UserServices\Schemas;

use App\Enums\Currency;
use App\Enums\UserType;
use App\Filament\Customs\ExpectedPriceField;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Service Information')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name', fn($query) => $query->where('user_type', UserType::HR))
                            ->searchable()
                            ->preload()
                            ->hidden(!auth()->user()->isSuperAdmin())
                            ->required()
                            ->helperText('Select the HR/Client user who owns this service'),

                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Consultation, Project Review, etc.')
                            ->afterStateUpdatedJs(<<<'JS'
                            $set('slug', ($state).replaceAll(' ', '-').toLowerCase());
                            JS),

                        TextInput::make('slug')
                            ->label('URL Slug')
                            ->maxLength(255)
                            ->unique('user_services', 'slug', ignoreRecord: true)
                            ->readonly()
                            ->helperText('Leave empty to auto-generate from name'),

                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        ExpectedPriceField::make('price')
                            ->nullable()
                            ->helperText('Leave empty if service is free'),

                        Select::make('price_currency')
                            ->label('Price Currency')
                            ->options(Currency::class)
                            ->default(Currency::IQD)
                            ->searchable()
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->required()
                            ->helperText('Whether this service is currently active'),

                        TextInput::make('time_minutes')
                            ->label('Duration (minutes)')
                            ->numeric()
                            ->suffix('minutes')
                            ->nullable()
                            ->helperText('Leave empty if duration is not applicable')
                            ->minValue(0),
                    ])
                    ->columns(2),
            ]);
    }
}
