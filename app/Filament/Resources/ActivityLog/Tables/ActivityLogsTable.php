<?php

namespace App\Filament\Resources\ActivityLog\Tables;

use App\Enums\ActivityLogEvent;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with([
                'subject' => function ($query) {
                    $query->withoutGlobalScopes();
                },
                'causer' => function ($query) {
                    $query->withoutGlobalScopes();
                },
            ])->latest())
            ->pushColumns([

                ImageColumn::make('causer.avatar_url')
                    ->label('الصورة')
                    ->circular(),

                TextColumn::make('causer.name')
                    ->label('المستخدم')
                    ->placeholder('نظام')
                    ->icon('heroicon-o-user')
                    ->searchable(),

                TextColumn::make('causer.email')
                    ->label('البريد الإلكتروني')
                    ->placeholder('نظام'),

                TextColumn::make('causer.phone')
                    ->label('الهاتف')
                    ->placeholder('نظام')
                    ->searchable(),

                TextColumn::make('event')
                    ->label('نوع العملية')
                    ->badge()
                    ->sortable(),

                TextColumn::make('subject.id')
                    ->label('رقم الموضوع')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subject_type')
                    ->label('نوع الموضوع')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subject.name')
                    ->label('الموضوع')
                    ->placeholder('غير متاح')
                    ->limit(30),

                TextColumn::make('description')
                    ->label('الوصف')
                    ->placeholder('لا يوجد وصف')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                TextColumn::make('log_name')
                    ->label('اسم السجل')
                    ->badge()
                    ->color('primary')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->pushFilters([
                SelectFilter::make('event')
                    ->label('نوع العملية')
                    ->options(ActivityLogEvent::class)
                    ->multiple(),

                SelectFilter::make('subject_id')
                    ->label('رقم الموضوع')
                    ->preload()
                    ->options(fn(Builder $query) => Activity::limit(50)->pluck('subject_id', 'subject_id'))
                    ->getSearchResultsUsing(fn(string $search) => Activity::where('subject_id', 'like', "%{$search}%")->limit(50)->pluck('subject_id', 'subject_id'))
                    ->searchable(),

                SelectFilter::make('subject_type')
                    ->options(fn() => collect(glob(app_path('Models') . '/*.php'))->map(fn($file) => 'App\Models\\' . basename($file, '.php'))->mapWithKeys(fn($class) => [$class => $class]))
                    ->label('نوع الموضوع'),

                SelectFilter::make('causer_id')
                    ->label('المستخدم')
                    ->preload()
                    ->options(function (Builder $query, $livewire) {
                        return User::limit(50)
                            ->when(isset($livewire->tableFilters['causer_id']['value']), function ($query) use ($livewire) {
                                return $query->where('id', $livewire->tableFilters['causer_id']['value']);
                            })
                            ->get()
                            ->pluck('name', 'id');
                    })
                    ->getSearchResultsUsing(fn(string $query) => User::where('name', 'like', "%{$query}%")->limit(50)->pluck('name', 'id'))
                    ->searchable(),

            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('عرض')
                        ->icon('heroicon-o-eye'),

                    Action::make('reverse-activity')
                        ->label('التراجع عن التغيير')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->visible(fn($record) => $record->event === ActivityLogEvent::Updated->value)
                        ->color('warning')
                        ->requiresConfirmation()
                        ->after(function () {
                            Notification::make()
                                ->title('تم التراجع عن التغيير بنجاح')
                                ->success()
                                ->send();
                        })
                        ->action(function ($record) {
                            $old = $record->properties['old'];
                            unset($old['updated_at']);
                            $record->subject->update($old);
                        }),
                ]),

            ])
            ->toolbarActions([
                BulkAction::make('reverse-bulk-activity')
                    ->label('التراجع عن التغييرات')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->after(function () {
                        Notification::make()
                            ->title('تم التراجع عن التغييرات بنجاح')
                            ->success()
                            ->send();
                    })
                    ->action(function ($records) {
                        $records->each(function ($record) {
                            $old = $record->properties['old'];
                            unset($old['updated_at']);
                            $record->subject->update($old);
                        });
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
