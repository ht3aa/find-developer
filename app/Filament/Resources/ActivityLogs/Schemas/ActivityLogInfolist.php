<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use App\Support\ActivityLogPresenter;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;

class ActivityLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Summary')
                    ->components([
                        TextEntry::make('id')
                            ->label('ID'),
                        TextEntry::make('description')
                            ->columnSpanFull(),
                        TextEntry::make('event')
                            ->badge(),
                        TextEntry::make('log_name')
                            ->label('Log name')
                            ->placeholder('default'),
                        TextEntry::make('batch_uuid')
                            ->label('Batch UUID')
                            ->placeholder('—'),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->label('Created'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->label('Updated'),
                    ]),
                Section::make('Subject')
                    ->components([
                        TextEntry::make('subject_type')
                            ->label('Type')
                            ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '—'),
                        TextEntry::make('subject_id')
                            ->label('ID')
                            ->placeholder('—'),
                        TextEntry::make('subject_label')
                            ->label('Label')
                            ->state(fn (Activity $record): ?string => ActivityLogPresenter::subjectLabel($record))
                            ->placeholder('—'),
                    ]),
                Section::make('Causer')
                    ->components([
                        TextEntry::make('causer_type')
                            ->label('Type')
                            ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '—'),
                        TextEntry::make('causer_id')
                            ->label('ID')
                            ->placeholder('—'),
                        TextEntry::make('causer_name')
                            ->label('Name / email')
                            ->state(fn (Activity $record): ?string => $record->causer?->name ?? $record->causer?->email ?? null)
                            ->placeholder('—'),
                    ]),
                Section::make('Properties')
                    ->columnSpanFull()
                    ->components([
                        TextEntry::make('properties')
                            ->label('Raw JSON')
                            ->columnSpanFull()
                            ->formatStateUsing(function ($state): string {
                                if ($state instanceof Collection) {
                                    $state = $state->toArray();
                                }

                                return json_encode($state ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                            }),
                    ]),
            ]);
    }
}
