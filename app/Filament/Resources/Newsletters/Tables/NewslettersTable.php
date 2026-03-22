<?php

namespace App\Filament\Resources\Newsletters\Tables;

use App\Services\NewsletterBulkMailService;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class NewslettersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Subscribed')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', direction: 'desc')
            ->defaultPaginationPageOption(20)
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('sendMailtrap')
                        ->label('Send Mailtrap email')
                        ->icon(Heroicon::OutlinedPaperAirplane)
                        ->form([
                            TextInput::make('title')
                                ->required()
                                ->maxLength(255),
                            Textarea::make('body')
                                ->required()
                                ->maxLength(5000)
                                ->rows(8),
                            TextInput::make('category')
                                ->maxLength(100)
                                ->placeholder('e.g. Newsletter, Notification'),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            try {
                                $count = app(NewsletterBulkMailService::class)->sendToIds(
                                    $records->pluck('id')->all(),
                                    $data['title'],
                                    $data['body'],
                                    $data['category'] ?? null,
                                );
                                Notification::make()
                                    ->title('Bulk email sent to '.$count.' subscriber(s) via Mailtrap.')
                                    ->success()
                                    ->send();
                            } catch (ValidationException $e) {
                                Notification::make()
                                    ->title('Could not send')
                                    ->body(collect($e->errors())->flatten()->first() ?? $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
