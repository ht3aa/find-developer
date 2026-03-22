<?php

namespace App\Filament\Resources\MessageAttachments\Pages;

use App\Filament\Resources\MessageAttachments\MessageAttachmentResource;
use Filament\Resources\Pages\ViewRecord;

class ViewMessageAttachment extends ViewRecord
{
    protected static string $resource = MessageAttachmentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
