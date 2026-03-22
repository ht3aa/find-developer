<?php

namespace App\Filament\Resources\Messages\Schemas;

use App\Filament\Resources\Conversations\ConversationResource;
use App\Models\Message;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Message')
                    ->components([
                        TextEntry::make('id')
                            ->label('ID'),
                        TextEntry::make('conversation_id')
                            ->label('Conversation')
                            ->formatStateUsing(fn ($state): string => $state ? '#'.$state : '—')
                            ->url(fn (Message $record): ?string => $record->conversation
                                ? ConversationResource::getUrl('view', ['record' => $record->conversation])
                                : null),
                        TextEntry::make('user_display')
                            ->label('Sender')
                            ->state(function (Message $record): string {
                                $user = $record->user;
                                if ($user === null) {
                                    return '—';
                                }

                                $type = $user->user_type?->getLabel() ?? '—';

                                return "{$user->name} ({$user->email}) — {$type}";
                            })
                            ->columnSpanFull(),
                        TextEntry::make('body')
                            ->label('Body')
                            ->html()
                            ->columnSpanFull(),
                        TextEntry::make('created_at')
                            ->dateTime(),
                    ]),
            ]);
    }
}
