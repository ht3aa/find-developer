<?php

namespace App\Filament\Resources\ExperienceTasks\Pages;

use App\Filament\Resources\ExperienceTasks\ExperienceTaskResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExperienceTask extends CreateRecord
{
    protected static string $resource = ExperienceTaskResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
