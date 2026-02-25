<?php

namespace App\Enums;

use Filament\Support\Icons\Heroicon;

enum SubscriptionPlan: string
{
    case FREE = 'free';
    case PRO = 'pro';
    case PREMIUM = 'premium';

    public function getLabel(): string
    {
        return match ($this) {
            self::FREE => 'Free',
            self::PRO => 'Pro',
            self::PREMIUM => 'Premium',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::FREE => 'gray',
            self::PRO => 'info',
            self::PREMIUM => 'warning',
        };
    }

    public function getIcon(): string|Heroicon
    {
        return match ($this) {
            self::FREE => Heroicon::OutlinedUser,
            self::PRO => Heroicon::OutlinedStar,
            self::PREMIUM => Heroicon::OutlinedSparkles,
        };
    }
}
