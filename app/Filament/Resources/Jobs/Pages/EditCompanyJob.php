<?php

namespace App\Filament\Resources\Jobs\Pages;

use App\Filament\Resources\Jobs\CompanyJobResource;
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
