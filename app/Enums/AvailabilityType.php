<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AvailabilityType: string implements HasColor, HasIcon, HasLabel
{
    case FULL_TIME = 'full-time';
    case PART_TIME = 'part-time';
    case FREELANCE = 'freelance';
    case HYBRID = 'hybrid';
    case REMOTE = 'remote';
    case REMOTE_FULL_TIME = 'remote-full-time';
    case HYBRID_FULL_TIME = 'hybrid-full-time';

    public function getLabel(): string
    {
        return match ($this) {
            self::FULL_TIME => 'Full-time',
            self::PART_TIME => 'Part-time',
            self::FREELANCE => 'Freelance',
            self::HYBRID => 'Hybrid',
            self::REMOTE => 'Remote',
            self::REMOTE_FULL_TIME => 'Remote Full-time',
            self::HYBRID_FULL_TIME => 'Hybrid Full-time',
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
            self::REMOTE_FULL_TIME => 'success',
            self::HYBRID_FULL_TIME => 'warning',
        };
    }

    public function getHexColor(): string
    {
        return match ($this) {
            self::FULL_TIME => '#10b981',
            self::PART_TIME => '#f59e0b',
            self::FREELANCE => '#3b82f6',
            self::HYBRID => '#7C3AED',
            self::REMOTE => '#2563eb',
            self::REMOTE_FULL_TIME => '#10b981',
            self::HYBRID_FULL_TIME => '#f5f30b',
            default => '#6b7280',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::FULL_TIME => 'heroicon-o-briefcase',
            self::PART_TIME => 'heroicon-o-clock',
            self::FREELANCE => 'heroicon-o-user',
            self::HYBRID => 'heroicon-o-computer-desktop',
            self::REMOTE => 'heroicon-o-globe-alt',
            self::REMOTE_FULL_TIME => 'heroicon-o-globe-alt',
            self::HYBRID_FULL_TIME => 'heroicon-o-computer-desktop',
        };
    }
}
