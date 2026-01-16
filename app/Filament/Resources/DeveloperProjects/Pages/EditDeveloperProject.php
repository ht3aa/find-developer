<?php

namespace App\Filament\Resources\DeveloperProjects\Pages;

use App\Filament\Resources\DeveloperProjects\DeveloperProjectResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeveloperProject extends EditRecord
{
    protected static string $resource = DeveloperProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
