<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ActivityLogEvent: string implements HasColor, HasIcon, HasLabel
{
    /**
     * created => إنشاء
     */
    case Created = 'created';

    /**
     * updated => تحديث
     */
    case Updated = 'updated';

    /**
     * deleted => حذف
     */
    case Deleted = 'deleted';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Created => 'إنشاء',
            self::Updated => 'تحديث',
            self::Deleted => 'حذف',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Created => 'success',
            self::Updated => 'warning',
            self::Deleted => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Created => 'heroicon-o-plus-circle',
            self::Updated => 'heroicon-o-pencil-square',
            self::Deleted => 'heroicon-o-trash',
        };
    }
}
