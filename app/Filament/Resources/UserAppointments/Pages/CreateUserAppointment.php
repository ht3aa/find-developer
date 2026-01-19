<?php

namespace App\Filament\Resources\UserAppointments\Pages;

use App\Filament\Resources\UserAppointments\UserAppointmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserAppointment extends CreateRecord
{
    protected static string $resource = UserAppointmentResource::class;
}
