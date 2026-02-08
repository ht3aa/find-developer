<?php

namespace App\Filament\Resources\UserAppointments\Schemas;

use App\Enums\AppointmentStatus;
use App\Enums\UserType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserAppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Appointment Information')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('user_id')
                            ->label('HR/Client User')
                            ->relationship('user', 'name', fn ($query) => $query->where('user_type', UserType::HR))
                            ->searchable()
                            ->preload()
                            ->hidden(! auth()->user()->isSuperAdmin())
                            ->required()
                            ->helperText('Select the HR/Client user who created this appointment'),

                        Select::make('developer_id')
                            ->label('Developer')
                            ->relationship('developer', 'name')
                            ->searchable()
                            ->preload()
                            ->hidden(! auth()->user()->isSuperAdmin())
                            ->required()
                            ->helperText('Select the developer for this appointment'),

                        Select::make('user_service_id')
                            ->label('Service')
                            ->relationship('service', 'name', fn ($query, $get) => $query->where('is_active', true)->where('user_id', $get('user_id')))
                            ->searchable()
                            ->preload()
                            ->hidden(! auth()->user()->isSuperAdmin())
                            ->nullable()
                            ->helperText('Optional: Link this appointment to a service'),

                        ToggleButtons::make('status')
                            ->options(AppointmentStatus::class)
                            ->default(AppointmentStatus::PENDING)
                            ->inline()
                            ->required()
                            ->helperText('Status of the appointment'),

                        DateTimePicker::make('start_datetime')
                            ->label('Start Date & Time')
                            ->nullable()
                            ->helperText('Leave empty until the user sets the appointment time')
                            ->timezone('UTC')
                            ->displayFormat('Y-m-d h:i A')
                            ->native(false),

                        Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Additional notes about the appointment'),
                    ])
                    ->columns(2),
            ]);
    }
}
