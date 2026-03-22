<?php

namespace App\Filament\Resources\DeveloperRecommendations\Tables;

use App\Enums\RecommendationStatus;
use App\Models\DeveloperRecommendation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DeveloperRecommendationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('recommender.name')
                    ->label('Recommender')
                    ->searchable()
                    ->sortable()
                    ->url(fn (DeveloperRecommendation $record): ?string => $record->recommender
                        ? route('developers.show', $record->recommender)
                        : null),
                TextColumn::make('recommended.name')
                    ->label('Recommended')
                    ->searchable()
                    ->sortable()
                    ->url(fn (DeveloperRecommendation $record): ?string => $record->recommended
                        ? route('developers.show', $record->recommended)
                        : null),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?RecommendationStatus $state): string => $state?->getLabel() ?? '')
                    ->color(fn (?RecommendationStatus $state): string => $state?->getColor() ?? 'gray')
                    ->sortable(),
                TextColumn::make('recommendation_note')
                    ->label('Note')
                    ->limit(60)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return is_string($state) && strlen($state) > 60 ? $state : null;
                    })
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', direction: 'desc')
            ->defaultPaginationPageOption(15)
            ->filters([
                SelectFilter::make('status')
                    ->options(RecommendationStatus::class),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
