<?php

namespace App\Filament\Resources\UserServices\Pages;

use App\Filament\Resources\UserServices\UserServiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserServices extends ListRecords
{
    protected static string $resource = UserServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
