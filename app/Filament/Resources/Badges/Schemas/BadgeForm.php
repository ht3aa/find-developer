<?php

namespace App\Filament\Resources\Badges\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BadgeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('slug', Str::slug($state));
                    })
                    ->required(),

                TextInput::make('slug')
                    ->label('Slug')
                    ->readOnly()
                    ->required(),
                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->maxLength(1000)
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('how_to_earn_description')
                    ->label('How to earn')
                    ->rows(4)
                    ->maxLength(5000)
                    ->helperText('Explain how developers can earn this badge.')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('icon')
                    ->label('Icon')
                    ->default(null),
                ColorPicker::make('color')
                    ->label('Color')
                    ->default('#000000')
                    ->required(),

                Toggle::make('is_active')
                    ->label('Active')
                    ->required(),
            ]);
    }
}
