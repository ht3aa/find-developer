<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ExperienceGain: int implements HasLabel
{
    case ZERO = 0;
    case FIVE = 5;
    case TEN = 10;
    case FIFTEEN = 15;
    case TWENTY = 20;

    case TWENTY_FIVE = 25;
    case THIRTY = 30;
    case THIRTY_FIVE = 35;
    case FOURTY = 40;
    case FOURTY_FIVE = 45;
    case FIFTY = 50;
    case FIFTY_FIVE = 55;
    case SIXTY = 60;
    case SIXTY_FIVE = 65;
    case SEVENTY = 70;
    case SEVENTY_FIVE = 75;
    case EIGHTY = 80;
    case EIGHTY_FIVE = 85;
    case NINETY = 90;

    public function getLabel(): string
    {
        return (string) $this->value;
    }
}
