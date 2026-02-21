<?php

namespace App\Filament\Resources\Companies\Schemas;

use App\Enums\CompanyStatus;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company Information')
                    ->description('Company logo, name and skills they are working on')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Set $set): void {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Auto-generated from name'),

                        FileUpload::make('logo_path')
                            ->label('Company Logo')
                            ->image()
                            ->imageEditor()
                            ->maxSize(1024)
                            ->directory('company-logos')
                            ->nullable(),

                        Select::make('status')
                            ->options(CompanyStatus::class)
                            ->default(CompanyStatus::ACTIVE)
                            ->required(),

                        Select::make('skills')
                            ->relationship('skills', 'name', fn ($query) => $query->where('is_active', true))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
