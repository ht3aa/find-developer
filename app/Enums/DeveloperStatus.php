<?php

namespace App\Enums;

enum DeveloperStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    case EXPERIENCE_CHANGED = 'experience_changed';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::EXPERIENCE_CHANGED => 'Experience Changed',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::EXPERIENCE_CHANGED => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::APPROVED => 'heroicon-o-check-circle',
            self::REJECTED => 'heroicon-o-x-circle',
            self::EXPERIENCE_CHANGED => 'heroicon-o-clock',
        };
    }
}
