<?php

namespace App\Filament\Resources\DeveloperCompanies\Pages;

use App\Filament\Resources\DeveloperCompanies\DeveloperCompanyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeveloperCompany extends EditRecord
{
    protected static string $resource = DeveloperCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
