<?php

namespace App\Filament\Resources\DeveloperProjects\Pages;

use App\Filament\Resources\DeveloperProjects\DeveloperProjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDeveloperProject extends CreateRecord
{
    protected static string $resource = DeveloperProjectResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->user()->isDeveloper()) {
            $data['developer_id'] = auth()->user()->developer?->id;
        }

        return $data;
    }
}
