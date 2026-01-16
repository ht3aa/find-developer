<?php

namespace App\Filament\Resources\DeveloperProjects\Pages;

use App\Filament\Resources\DeveloperProjects\DeveloperProjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeveloperProjects extends ListRecords
{
    protected static string $resource = DeveloperProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
