<?php

namespace App\Enums;

enum BlogStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
            self::ARCHIVED => 'Archived',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PUBLISHED => 'success',
            self::ARCHIVED => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::DRAFT => 'heroicon-o-document-text',
            self::PUBLISHED => 'heroicon-o-check-circle',
            self::ARCHIVED => 'heroicon-o-archive-box',
        };
    }
}
