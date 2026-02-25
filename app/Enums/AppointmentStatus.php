<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::CONFIRMED => 'info',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::CONFIRMED => 'heroicon-o-check-circle',
            self::COMPLETED => 'heroicon-o-check-badge',
            self::CANCELLED => 'heroicon-o-x-circle',
        };
    }
}
