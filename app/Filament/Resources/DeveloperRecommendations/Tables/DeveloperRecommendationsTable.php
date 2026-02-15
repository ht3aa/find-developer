<?php

namespace App\Filament\Resources\DeveloperRecommendations\Tables;

use App\Enums\RecommendationStatus;
use App\Notifications\MailtrapNotification;
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

                    Action::make('processing')
                        ->label('Processing')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->status !== RecommendationStatus::PROCESSING)
                        ->action(function ($record) {
                            $record->update(['status' => RecommendationStatus::PROCESSING]);

                            $record->load(['recommender', 'recommended']);

                            $replyInstruction = "\n\nPlease send your answer to ht3aa2001@gmail.com";

                            $recommenderMessage = "Hello {$record->recommender->name},\n\n";
                            $recommenderMessage .= "We are reviewing your recommendation for {$record->recommended->name} ({$record->recommended->email})\n\n";
                            $recommenderMessage .= 'Could you please tell us: How did you get to know this developer?';
                            $recommenderMessage .= $replyInstruction;

                            $record->recommender->notify(new MailtrapNotification(
                                subject: 'How did you know this developer?',
                                message: $recommenderMessage,
                                category: 'Recommendation Processing'
                            ));

                            $recommendedMessage = "Hello {$record->recommended->name},\n\n";
                            $recommendedMessage .= "You have been recommended by {$record->recommender->name} ({$record->recommender->email}).\n\n";
                            $recommendedMessage .= 'Could you please tell us: How did you get to know the person who recommended you?';
                            $recommendedMessage .= $replyInstruction;

                            $record->recommended->notify(new MailtrapNotification(
                                subject: 'How did you know the person who recommended you?',
                                message: $recommendedMessage,
                                category: 'Recommendation Processing'
                            ));

                            Notification::make()
                                ->title('Recommendation Processing')
                                ->body('Status updated and inquiry emails sent to recommender and recommended developer.')
                                ->success()
                                ->send();
                        }),

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
            ->toolbarActions([
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
