<?php

namespace App\Filament\Resources\DeveloperCompanies\Pages;

use App\Filament\Resources\DeveloperCompanies\DeveloperCompanyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDeveloperCompany extends CreateRecord
{
    protected static string $resource = DeveloperCompanyResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->user()->isDeveloper()) {
            $data['developer_id'] = auth()->user()->developer?->id;
        }

        return $data;
    }
}
