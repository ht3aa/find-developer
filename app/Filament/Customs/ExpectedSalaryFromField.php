<?php

namespace App\Filament\Customs;

use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

class ExpectedSalaryFromField
{
    public static function make(string $name = 'expected_salary_from'): TextInput
    {
        return TextInput::make($name)
            ->mask(RawJs::make('$money($input)'))
            ->stripCharacters(',')
            ->label('Expected Salary From')
            ->lt('expected_salary_to')
            ->regex('/^\d+$/')
            ->minValue(0);
    }

    public static function makeWithoutLt(string $name = 'expected_salary_from'): TextInput
    {
        return TextInput::make($name)
            ->mask(RawJs::make('$money($input)'))
            ->stripCharacters(',')
            ->label('Expected Salary From')
            ->regex('/^\d+$/')
            ->minValue(0);
    }
}
