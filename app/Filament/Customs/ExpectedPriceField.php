<?php

namespace App\Filament\Customs;

use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

class ExpectedPriceField
{
    public static function make(string $name = 'price'): TextInput
    {
        return TextInput::make($name)
            ->mask(RawJs::make('$money($input)'))
            ->stripCharacters(',')
            ->label('Price')
            ->regex('/^\d+$/')
            ->minValue(0);
    }
}
