<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use App\Actions\SuspendActivityCauserAction;
use App\Models\Developer;
use App\Models\User;
use App\Support\ActivityLogPresenter;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\Models\Activity;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('event')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(60)
                    ->searchable()
                    ->wrap(),
                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '—')
                    ->searchable(),
                TextColumn::make('subject_id')
                    ->label('Subject ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('causer_email')
                    ->label('Causer email')
                    ->state(fn (Activity $record): ?string => ActivityLogPresenter::causerEmail($record))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $term = '%'.addcslashes($search, '%_\\').'%';

                        return $query->whereHasMorph(
                            'causer',
                            [User::class, Developer::class],
                            fn (Builder $sub) => $sub->where('email', 'like', $term),
                        );
                    }),
                TextColumn::make('log_name')
                    ->label('Log')
                    ->placeholder('default')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', direction: 'desc')
            ->defaultPaginationPageOption(20)
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Log name')
                    ->options(fn (): array => Activity::query()
                        ->whereNotNull('log_name')
                        ->distinct()
                        ->orderBy('log_name')
                        ->pluck('log_name', 'log_name')
                        ->all()),
                Filter::make('sort_by_causer')
                    ->label('Sort by causer')
                    ->toggle()
                    ->query(function (Builder $query, array $data): Builder {
                        if (! ($data['isActive'] ?? false)) {
                            return $query;
                        }

                        $query->reorder();

                        return $query
                            ->orderBy('causer_type')
                            ->orderBy('causer_id')
                            ->orderByDesc('created_at');
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('suspendDeveloper')
                    ->label('Suspend')
                    ->icon(Heroicon::OutlinedUserMinus)
                    ->color('danger')
                    ->visible(fn (Activity $record): bool => ActivityLogPresenter::canSuspendCauser($record))
                    ->requiresConfirmation()
                    ->modalHeading('Suspend developer profile')
                    ->modalDescription('This sets the linked developer profile to suspended.')
                    ->action(function (Activity $record): void {
                        try {
                            app(SuspendActivityCauserAction::class)(
                                (string) $record->causer_type,
                                (int) $record->causer_id,
                            );
                        } catch (ValidationException $e) {
                            $message = $e->errors()['causer'][0] ?? $e->getMessage();
                            Notification::make()
                                ->title('Could not suspend')
                                ->body($message)
                                ->danger()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Developer profile suspended')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
