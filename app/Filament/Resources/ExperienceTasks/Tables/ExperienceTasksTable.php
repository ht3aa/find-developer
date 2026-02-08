<?php

namespace App\Filament\Resources\ExperienceTasks\Tables;

use App\Enums\ExperienceTaskStatus;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExperienceTasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('requirements')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('rewards')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('required_developers_count')
                    ->label('Required')
                    ->sortable(),

                TextColumn::make('price')
                    ->formatStateUsing(fn ($state, $record) => $state
                        ? number_format($state).' '.($record->price_currency?->value ?? 'IQD')
                        : '-')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('experience_gain')
                    ->label('XP')
                    ->formatStateUsing(fn ($state) => $state?->getLabel() ?? '-')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('developers_count')
                    ->label('Assigned')
                    ->counts('developers')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ExperienceTaskStatus::class)
                    ->label('Status'),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ]);
    }
}
