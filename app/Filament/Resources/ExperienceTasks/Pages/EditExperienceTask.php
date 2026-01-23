<?php

namespace App\Filament\Resources\ExperienceTasks\Pages;

use App\Filament\Resources\ExperienceTasks\ExperienceTaskResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExperienceTask extends EditRecord
{
    protected static string $resource = ExperienceTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

}
