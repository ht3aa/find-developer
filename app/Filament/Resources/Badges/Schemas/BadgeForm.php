<?php

namespace App\Filament\Resources\Badges\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BadgeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Badge Information')
                    ->description('Manage badge details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Soft Skills Badge, CV Professional Badge')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Set $set) {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated()
                            ->unique(ignoreRecord: true)
                            ->helperText('Auto-generated from name'),

                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Describe what this badge represents'),

                        TextInput::make('icon')
                            ->maxLength(255)
                            ->placeholder('e.g., heroicon-o-academic-cap')
                            ->helperText('Icon name (e.g., heroicon name or custom icon identifier)'),

                        ColorPicker::make('color')
                            ->label('Badge Color')
                            ->default('primary')
                            ->helperText('Color theme for the badge'),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive badges won\'t be available for selection'),
                    ])
                    ->columns(2),
            ]);
    }
}
