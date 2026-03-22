<?php

namespace App\Filament\Resources\Newsletters\Pages;

use App\Filament\Resources\Newsletters\NewsletterResource;
use App\Services\NewsletterBulkMailService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\ValidationException;

class ListNewsletters extends ListRecords
{
    protected static string $resource = NewsletterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('emailAllSubscribers')
                ->label('Email all subscribers')
                ->icon(Heroicon::OutlinedEnvelope)
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
                ->action(function (array $data): void {
                    try {
                        $count = app(NewsletterBulkMailService::class)->sendToAll(
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
                }),
        ];
    }
}
