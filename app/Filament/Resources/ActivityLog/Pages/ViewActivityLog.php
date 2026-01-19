<?php

namespace App\Filament\Resources\ActivityLog\Pages;

use App\Enums\ActivityLogEvent;
use App\Filament\Customs\ActivityLogJson;
use App\Filament\Resources\ActivityLog\ActivityLogResource;
use Filament\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Image;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Schema;

class ViewActivityLog extends ViewRecord
{
    protected static string $resource = ActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Grid::make(2)
                    ->schema([
                        Section::make('تفاصيل النشاط')
                            ->description('معلومات أساسية حول النشاط المسجل')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('description')
                                            ->label('الوصف')
                                            ->placeholder('لا يوجد وصف'),

                                        TextEntry::make('created_at')
                                            ->label('تاريخ النشاط')
                                            ->dateTime('d/m/Y H:i:s')
                                            ->icon('heroicon-o-clock'),

                                        TextEntry::make('log_name')
                                            ->label('اسم السجل')
                                            ->badge()
                                            ->color('primary'),
                                    ]),
                            ])
                            ->columnSpan(6),
                        Section::make('الموضوع المتأثر')
                            ->description('معلومات حول الكائن الذي تم تعديله')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('subject_type')
                                            ->label('نوع الموضوع')
                                            ->badge()
                                            ->color('info'),

                                        TextEntry::make('subject.id')
                                            ->label('معرف الموضوع')
                                            ->placeholder('غير محدد')
                                            ->numeric(),

                                        TextEntry::make('subject.name')
                                            ->label('اسم الموضوع')
                                            ->placeholder('غير متاح')
                                            ->columnSpanFull(),
                                    ]),

                            ])
                            ->columnSpan(6)
                            ->collapsible(),

                    ])
                    ->columns(12),
                Section::make('المستخدم المسؤول')
                    ->description('معلومات المستخدم الذي قام بالعملية')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Flex::make([
                            Grid::make(2)
                                ->schema([

                                    TextEntry::make('causer.name')
                                        ->label('اسم المستخدم')
                                        ->placeholder('نظام')
                                        ->icon('heroicon-o-user'),

                                    TextEntry::make('causer.email')
                                        ->label('البريد الإلكتروني')
                                        ->placeholder('غير متاح')
                                        ->copyable()
                                        ->icon('heroicon-o-envelope'),

                                    TextEntry::make('causer.phone')
                                        ->label('الهاتف')
                                        ->placeholder('غير متاح')
                                        ->copyable()
                                        ->icon('heroicon-o-phone'),

                                    TextEntry::make('causer.user_type')
                                        ->label('نوع المستخدم')
                                        ->placeholder('غير متاح')
                                        ->badge()
                                        ->color('info'),

                                ]),

                            Grid::make(2)
                                ->schema([
                                    ImageEntry::make('causer.avatar_url')
                                        ->label('الصورة الشخصية')
                                        ->circular(),

                                    ImageEntry::make('causer.logo')
                                        ->label('الشعار')
                                        ->circular(),
                                ]),
                        ]),

                    ])
                    ->collapsible(),

                Section::make('تفاصيل التغييرات')
                    ->description('القيم القديمة والجديدة للحقول المعدلة')
                    ->icon('heroicon-o-arrow-path')
                    ->columnSpanFull()
                    ->schema([
                        ActivityLogJson::make('properties')
                            ->label('التغييرات')
                            ->placeholder('لا توجد قيم قديمة')
                            ->columnSpanFull()
                            ->keyLabel('الحقل')
                            ->valueLabel('القيمة الجديدة'),
                    ])
                    ->collapsible()
                    ->visible(
                        fn($record) => ! empty($record->properties['old']) || ! empty($record->properties['attributes'])
                    ),

            ]);
    }
}
