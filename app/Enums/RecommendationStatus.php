<?php

namespace App\Enums;

enum RecommendationStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';

    case PROCESSING = 'processing';
    case REJECTED = 'rejected';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::PROCESSING => 'Processing',
            self::REJECTED => 'Rejected',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::PROCESSING => 'warning',
            self::REJECTED => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::APPROVED => 'heroicon-o-check-circle',
            self::PROCESSING => 'heroicon-o-clock',
            self::REJECTED => 'heroicon-o-x-circle',
        };
    }
}
