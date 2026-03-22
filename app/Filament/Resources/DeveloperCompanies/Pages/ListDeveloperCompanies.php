<?php

namespace App\Filament\Resources\DeveloperCompanies\Pages;

use App\Filament\Resources\DeveloperCompanies\DeveloperCompanyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeveloperCompanies extends ListRecords
{
    protected static string $resource = DeveloperCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
