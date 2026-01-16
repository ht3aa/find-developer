<?php

namespace App\Filament\Resources\Developers\Tables;

use App\Enums\DeveloperStatus;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class DevelopersTable
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
                    ->toggleable(),

                TextColumn::make('jobTitle.name')
                    ->label('Job Title')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                TextColumn::make('years_of_experience')
                    ->label('Experience')
                    ->sortable()
                    ->suffix(' years'),

                TextColumn::make('expected_salary_from')
                    ->label('Salary From')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state) return '-';
                        return number_format($state) . ' ' . $record->currency;
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('expected_salary_to')
                    ->label('Salary To')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state) return '-';
                        return number_format($state) . ' ' . $record->currency;
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('location')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('subscription_plan')
                    ->label('Plan')
                    ->badge()
                    ->sortable(),

                IconColumn::make('is_available')
                    ->label('Available')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(DeveloperStatus::class)
                    ->label('Status'),

                SelectFilter::make('job_title_id')
                    ->relationship('jobTitle', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Job Title'),

                TernaryFilter::make('is_available')
                    ->label('Availability')
                    ->boolean()
                    ->trueLabel('Available only')
                    ->falseLabel('Unavailable only')
                    ->native(false),

                Filter::make('years_of_experience')
                    ->form([
                        TextInput::make('min_experience')
                            ->numeric()
                            ->label('Minimum Years'),
                        TextInput::make('max_experience')
                            ->numeric()
                            ->label('Maximum Years'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['min_experience'], fn($query, $value) => $query->where('years_of_experience', '>=', $value))
                            ->when($data['max_experience'], fn($query, $value) => $query->where('years_of_experience', '<=', $value));
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn($record) => $record->status !== DeveloperStatus::APPROVED)
                        ->action(function ($record) {
                            $record->update(['status' => DeveloperStatus::APPROVED]);

                            Notification::make()
                                ->title('Developer Approved')
                                ->body("Developer {$record->name} has been approved.")
                                ->success()
                                ->send();
                        }),

                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn($record) => $record->status !== DeveloperStatus::REJECTED)
                        ->action(function ($record) {
                            $record->update(['status' => DeveloperStatus::REJECTED]);

                            Notification::make()
                                ->title('Developer Rejected')
                                ->body("Developer {$record->name} has been rejected.")
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
                                $record->update(['status' => DeveloperStatus::APPROVED]);
                            });

                            Notification::make()
                                ->title('Developers Approved')
                                ->body("{$count} developer(s) have been approved.")
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
                                $record->update(['status' => DeveloperStatus::REJECTED]);
                            });

                            Notification::make()
                                ->title('Developers Rejected')
                                ->body("{$count} developer(s) have been rejected.")
                                ->warning()
                                ->send();
                        }),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
