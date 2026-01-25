<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AvailabilityType: string implements HasLabel, HasColor, HasIcon
{
    case FULL_TIME = 'full-time';
    case PART_TIME = 'part-time';
    case FREELANCE = 'freelance';

    case HYBRID = 'hybrid';

    case REMOTE = 'remote';

    public function getLabel(): string
    {
        return match ($this) {
            self::FULL_TIME => 'Full-time',
            self::PART_TIME => 'Part-time',
            self::FREELANCE => 'Freelance',
            self::HYBRID => 'Hybrid',
            self::REMOTE => 'Remote',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::FULL_TIME => 'success',
            self::PART_TIME => 'warning',
            self::FREELANCE => 'info',
            self::HYBRID => 'warning',
            self::REMOTE => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::FULL_TIME => 'heroicon-o-briefcase',
            self::PART_TIME => 'heroicon-o-clock',
            self::FREELANCE => 'heroicon-o-user',
            self::HYBRID => 'heroicon-o-computer-desktop',
            self::REMOTE => 'heroicon-o-globe',
        };
    }
}
