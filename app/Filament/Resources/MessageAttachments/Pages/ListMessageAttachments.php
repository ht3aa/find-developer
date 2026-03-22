<?php

namespace App\Filament\Resources\MessageAttachments\Pages;

use App\Filament\Resources\MessageAttachments\MessageAttachmentResource;
use Filament\Resources\Pages\ListRecords;

class ListMessageAttachments extends ListRecords
{
    protected static string $resource = MessageAttachmentResource::class;
}
