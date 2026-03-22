<?php

namespace App\Filament\Resources\DeveloperCompanies\Pages;

use App\Filament\Resources\DeveloperCompanies\DeveloperCompanyResource;
use App\Filament\Resources\DeveloperCompanies\Schemas\DeveloperCompanyForm;
use App\Models\DeveloperCompany;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditDeveloperCompany extends EditRecord
{
    protected static string $resource = DeveloperCompanyResource::class;

    public function form(Schema $schema): Schema
    {
        return DeveloperCompanyForm::configure($schema, $this->record->getKey());
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var DeveloperCompany $record */
        $record = $this->record;

        return DeveloperCompanyForm::prepareAndValidate($data, $record);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
