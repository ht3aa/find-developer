<?php

namespace App\Filament\Resources\Badges\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BadgeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true),
                Textarea::make('description')
                    ->maxLength(1000)
                    ->columnSpanFull(),
                TextInput::make('icon')
                    ->maxLength(255)
                    ->helperText('Optional heroicon or icon name.'),
                TextInput::make('color')
                    ->maxLength(50)
                    ->placeholder('e.g. primary, success, danger')
                    ->default('primary'),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}
