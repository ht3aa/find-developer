<?php

namespace App\Filament\Resources\DeveloperOffers\Tables;

use App\Enums\OfferStatus;
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

class DeveloperOffersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('developer.name')
                    ->label('Developer')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->developer->jobTitle->name ?? null),

                TextColumn::make('user.name')
                    ->label('Submitted By')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jobTitle.name')
                    ->label('Position')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('salary_range')
                    ->label('Salary')
                    ->placeholder('N/A')
                    ->toggleable(),

                TextColumn::make('work_type')
                    ->label('Work Type')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                    ->options(OfferStatus::class)
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
                        ->visible(fn ($record) => $record->status !== OfferStatus::APPROVED)
                        ->action(function ($record) {
                            $record->update(['status' => OfferStatus::APPROVED]);

                            Notification::make()
                                ->title('Offer Approved')
                                ->body("Offer from {$record->company_name} for {$record->developer->name} has been approved. An email has been sent to the developer.")
                                ->success()
                                ->send();
                        }),

                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->status !== OfferStatus::REJECTED)
                        ->action(function ($record) {
                            $record->update(['status' => OfferStatus::REJECTED]);

                            Notification::make()
                                ->title('Offer Rejected')
                                ->body("Offer from {$record->company_name} for {$record->developer->name} has been rejected.")
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
                                $record->update(['status' => OfferStatus::APPROVED]);
                            });

                            Notification::make()
                                ->title('Offers Approved')
                                ->body("{$count} offer(s) have been approved.")
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
                                $record->update(['status' => OfferStatus::REJECTED]);
                            });

                            Notification::make()
                                ->title('Offers Rejected')
                                ->body("{$count} offer(s) have been rejected.")
                                ->warning()
                                ->send();
                        }),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
