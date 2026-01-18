<?php

namespace App\Filament\Resources\Jobs\Pages;

use App\Filament\Resources\Jobs\CompanyJobResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanyJobs extends ListRecords
{
    protected static string $resource = CompanyJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
