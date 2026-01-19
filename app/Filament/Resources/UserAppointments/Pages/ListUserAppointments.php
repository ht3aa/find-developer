<?php

namespace App\Filament\Resources\UserAppointments\Pages;

use App\Filament\Resources\UserAppointments\UserAppointmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserAppointments extends ListRecords
{
    protected static string $resource = UserAppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
