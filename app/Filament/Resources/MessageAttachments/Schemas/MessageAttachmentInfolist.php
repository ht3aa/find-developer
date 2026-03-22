<?php

namespace App\Filament\Resources\MessageAttachments\Schemas;

use App\Filament\Resources\Conversations\ConversationResource;
use App\Filament\Resources\Messages\MessageResource;
use App\Models\MessageAttachment;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Number;

class MessageAttachmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('File')
                    ->components([
                        TextEntry::make('id')
                            ->label('ID'),
                        TextEntry::make('file_name')
                            ->columnSpanFull(),
                        TextEntry::make('file_type'),
                        TextEntry::make('file_size')
                            ->label('Size')
                            ->formatStateUsing(fn (?int $state): string => $state !== null
                                ? Number::fileSize($state)
                                : '—'),
                        TextEntry::make('file_url')
                            ->label('Open file')
                            ->url(fn (MessageAttachment $record): ?string => $record->file_url)
                            ->openUrlInNewTab()
                            ->placeholder('—'),
                        TextEntry::make('created_at')
                            ->dateTime(),
                    ]),
                Section::make('Context')
                    ->components([
                        TextEntry::make('message_id')
                            ->label('Message')
                            ->formatStateUsing(fn ($state): string => $state ? '#'.$state : '—')
                            ->url(fn (MessageAttachment $record): ?string => $record->message
                                ? MessageResource::getUrl('view', ['record' => $record->message])
                                : null),
                        TextEntry::make('conversation_ref')
                            ->label('Conversation')
                            ->state(fn (MessageAttachment $record): string => $record->message?->conversation_id
                                ? '#'.$record->message->conversation_id
                                : '—')
                            ->url(fn (MessageAttachment $record): ?string => $record->message?->conversation
                                ? ConversationResource::getUrl('view', ['record' => $record->message->conversation])
                                : null),
                        TextEntry::make('sender_display')
                            ->label('Sender')
                            ->state(function (MessageAttachment $record): string {
                                $user = $record->message?->user;
                                if ($user === null) {
                                    return '—';
                                }

                                return "{$user->name} ({$user->email})";
                            })
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
