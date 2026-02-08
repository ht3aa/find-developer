<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum UserType: string implements HasColor, HasIcon, HasLabel
{
    case DEVELOPER = 'developer';
    case ADMIN = 'admin';
    case HR = 'hr';

    public function getLabel(): string
    {
        return match ($this) {
            self::DEVELOPER => 'Developer',
            self::ADMIN => 'Admin',
            self::HR => 'HR',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DEVELOPER => 'info',
            self::ADMIN => 'danger',
            self::HR => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::DEVELOPER => 'heroicon-o-code-bracket',
            self::ADMIN => 'heroicon-o-shield-check',
            self::HR => 'heroicon-o-briefcase',
        };
    }
}
