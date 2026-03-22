<?php

namespace App\Filament\Resources\Conversations\Schemas;

use App\Models\Conversation;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ConversationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Overview')
                    ->components([
                        TextEntry::make('id')
                            ->label('ID'),
                        TextEntry::make('participants_display')
                            ->label('Participants')
                            ->state(fn (Conversation $record): string => $record->participants
                                ->map(fn ($p) => "{$p->name} ({$p->email})")
                                ->join(', '))
                            ->columnSpanFull(),
                        TextEntry::make('messages_count')
                            ->label('Messages')
                            ->state(fn (Conversation $record): int => $record->messages()->count()),
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ]),
            ]);
    }
}
