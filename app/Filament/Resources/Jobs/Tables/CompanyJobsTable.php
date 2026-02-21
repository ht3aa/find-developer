<?php

namespace App\Filament\Resources\Jobs\Tables;

use App\Enums\JobStatus;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class CompanyJobsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jobTitle.name')
                    ->label('Job Title')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('location')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('salary_from')
                    ->label('Salary From')
                    ->formatStateUsing(function ($state, $record) {
                        if (! $state) {
                            return '-';
                        }

                        return number_format($state).' '.($record->salary_currency?->value ?? 'IQD');
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('salary_to')
                    ->label('Salary To')
                    ->formatStateUsing(function ($state, $record) {
                        if (! $state) {
                            return '-';
                        }

                        return number_format($state).' '.($record->salary_currency?->value ?? 'IQD');
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(JobStatus::class)
                    ->label('Status'),

                SelectFilter::make('job_title_id')
                    ->relationship('jobTitle', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Job Title'),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->status !== JobStatus::APPROVED)
                        ->action(function ($record) {
                            $record->update(['status' => JobStatus::APPROVED]);

                            Notification::make()
                                ->title('Job Approved')
                                ->body("Job \"{$record->title}\" has been approved.")
                                ->success()
                                ->send();
                        }),

                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->status !== JobStatus::REJECTED)
                        ->action(function ($record) {
                            $record->update(['status' => JobStatus::REJECTED]);

                            Notification::make()
                                ->title('Job Rejected')
                                ->body("Job \"{$record->title}\" has been rejected.")
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
                                $record->update(['status' => JobStatus::APPROVED]);
                            });

                            Notification::make()
                                ->title('Jobs Approved')
                                ->body("{$count} job(s) have been approved.")
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
                                $record->update(['status' => JobStatus::REJECTED]);
                            });

                            Notification::make()
                                ->title('Jobs Rejected')
                                ->body("{$count} job(s) have been rejected.")
                                ->warning()
                                ->send();
                        }),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
