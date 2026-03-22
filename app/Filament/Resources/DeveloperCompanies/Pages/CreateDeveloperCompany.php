<?php

namespace App\Filament\Resources\DeveloperCompanies\Pages;

use App\Filament\Resources\DeveloperCompanies\DeveloperCompanyResource;
use App\Filament\Resources\DeveloperCompanies\Schemas\DeveloperCompanyForm;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateDeveloperCompany extends CreateRecord
{
    protected static string $resource = DeveloperCompanyResource::class;

    public function form(Schema $schema): Schema
    {
        return DeveloperCompanyForm::configure($schema, null);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return DeveloperCompanyForm::prepareAndValidate($data);
    }
}
