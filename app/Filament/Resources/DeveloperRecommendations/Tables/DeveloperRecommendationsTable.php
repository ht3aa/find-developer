<?php

namespace App\Filament\Resources\DeveloperRecommendations\Tables;

use App\Enums\RecommendationStatus;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class DeveloperRecommendationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('recommender.name')
                    ->label('Recommender')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->recommender->jobTitle->name ?? null),

                TextColumn::make('recommended.name')
                    ->label('Recommended Developer')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->recommended->jobTitle->name ?? null),

                TextColumn::make('recommendation_note')
                    ->label('Recommendation Note')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->recommendation_note)
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(RecommendationStatus::class)
                    ->label('Status'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),

                    Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->status !== RecommendationStatus::APPROVED)
                        ->action(function ($record) {
                            $record->update(['status' => RecommendationStatus::APPROVED]);

                            Notification::make()
                                ->title('Recommendation Approved')
                                ->body("Recommendation from {$record->recommender->name} for {$record->recommended->name} has been approved.")
                                ->success()
                                ->send();
                        }),

                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->status !== RecommendationStatus::REJECTED)
                        ->action(function ($record) {
                            $record->update(['status' => RecommendationStatus::REJECTED]);

                            Notification::make()
                                ->title('Recommendation Rejected')
                                ->body("Recommendation from {$record->recommender->name} for {$record->recommended->name} has been rejected.")
                                ->warning()
                                ->send();
                        }),

                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $count = $records->count();

                            $records->each(function ($record) {
                                $record->update(['status' => RecommendationStatus::APPROVED]);
                            });

                            Notification::make()
                                ->title('Recommendations Approved')
                                ->body("{$count} recommendation(s) have been approved.")
                                ->success()
                                ->send();
                        }),

                    BulkAction::make('reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $count = $records->count();

                            $records->each(function ($record) {
                                $record->update(['status' => RecommendationStatus::REJECTED]);
                            });

                            Notification::make()
                                ->title('Recommendations Rejected')
                                ->body("{$count} recommendation(s) have been rejected.")
                                ->warning()
                                ->send();
                        }),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
