<?php

namespace App\Filament\Resources\CompanyJobs\Pages;

use App\Filament\Resources\CompanyJobs\CompanyJobResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCompanyJob extends EditRecord
{
    protected static string $resource = CompanyJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
