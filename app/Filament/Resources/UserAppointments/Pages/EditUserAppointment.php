<?php

namespace App\Filament\Resources\UserAppointments\Pages;

use App\Filament\Resources\UserAppointments\UserAppointmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUserAppointment extends EditRecord
{
    protected static string $resource = UserAppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
