<?php

namespace App\Filament\Resources\UserAppointments\Tables;

use App\Enums\AppointmentStatus;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UserAppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('HR/Client User')
                    ->searchable()
                    ->hidden(!auth()->user()->isSuperAdmin())
                    ->sortable(),

                TextColumn::make('developer.name')
                    ->label('Developer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('service.name')
                    ->label('Service')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->default('-'),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('start_datetime')
                    ->label('Start Date & Time')
                    ->dateTime('Y-m-d h:i A')
                    ->sortable()
                    ->placeholder('Not set'),

                TextColumn::make('notes')
                    ->limit(30)
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(AppointmentStatus::class)
                    ->label('Status'),

                SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->label('HR/Client User'),

                SelectFilter::make('developer_id')
                    ->relationship('developer', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Developer'),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ]);
    }
}
