<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Email copied!')
                    ->copyMessageDuration(1500),

                TextColumn::make('user_type')
                    ->label('User Type')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('roles.name')
                    ->badge()
                    ->separator(','),

                IconColumn::make('can_access_admin_panel')
                    ->label('Admin Access')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('toggle_admin_access')
                        ->label(fn ($record) => $record->can_access_admin_panel ? 'Revoke Admin Access' : 'Grant Admin Access')
                        ->icon(fn ($record) => $record->can_access_admin_panel ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn ($record) => $record->can_access_admin_panel ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $wasEnabled = $record->can_access_admin_panel;
                            $record->update([
                                'can_access_admin_panel' => ! $wasEnabled,
                            ]);
                        }),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('grant_admin_access')
                        ->label('Grant Admin Access')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['can_access_admin_panel' => true]);
                            });
                        })
                        ->successNotificationTitle(fn (Collection $records) => 'Admin access granted to '.$records->count().' user(s)'),
                    BulkAction::make('revoke_admin_access')
                        ->label('Revoke Admin Access')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['can_access_admin_panel' => false]);
                            });
                        })
                        ->successNotificationTitle(fn (Collection $records) => 'Admin access revoked from '.$records->count().' user(s)'),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
