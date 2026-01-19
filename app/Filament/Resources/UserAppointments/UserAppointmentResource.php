<?php

namespace App\Filament\Resources\UserAppointments;

use App\Filament\Resources\UserAppointments\Pages\CreateUserAppointment;
use App\Filament\Resources\UserAppointments\Pages\EditUserAppointment;
use App\Filament\Resources\UserAppointments\Pages\ListUserAppointments;
use App\Filament\Resources\UserAppointments\Schemas\UserAppointmentForm;
use App\Filament\Resources\UserAppointments\Tables\UserAppointmentsTable;
use App\Models\UserAppointment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserAppointmentResource extends Resource
{
    protected static ?string $model = UserAppointment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return UserAppointmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserAppointmentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUserAppointments::route('/'),
            'create' => CreateUserAppointment::route('/create'),
            'edit' => EditUserAppointment::route('/{record}/edit'),
        ];
    }
}
