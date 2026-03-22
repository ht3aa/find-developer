<?php

namespace App\Filament\Resources\Hackathons\Resources\HackathonTeams\Resources\HackathonTeamMembers\Tables;

use App\Enums\HackathonMemberPosition;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HackathonTeamMembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('developer.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('position')
                    ->badge()
                    ->formatStateUsing(function ($state): string {
                        if ($state instanceof HackathonMemberPosition) {
                            return $state->label();
                        }

                        return HackathonMemberPosition::tryFrom((string) $state)?->label() ?? '';
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
