<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ExperienceGain: int implements HasLabel
{
    case ZERO = 0;
    case TEN = 10;
    case TWENTY = 20;
    case THIRTY = 30;
    case FORTY = 40;
    case FIFTY = 50;
    case SIXTY = 60;
    case SEVENTY = 70;
    case EIGHTY = 80;
    case NINETY = 90;
    case HUNDRED = 100;

    public function getLabel(): string
    {
        return (string) $this->value;
    }
}
