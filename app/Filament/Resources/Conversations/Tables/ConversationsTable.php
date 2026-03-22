<?php

namespace App\Filament\Resources\Conversations\Tables;

use App\Models\Conversation;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ConversationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('participants_display')
                    ->label('Participants')
                    ->state(fn (Conversation $record): string => $record->participants
                        ->pluck('name')
                        ->join(', ')),
                TextColumn::make('messages_count')
                    ->counts('messages')
                    ->label('Messages')
                    ->sortable(),
                TextColumn::make('last_message_preview')
                    ->label('Last message')
                    ->state(function (Conversation $record): string {
                        $last = $record->lastMessage;

                        if ($last === null) {
                            return '—';
                        }

                        $body = $last->body ?? '';
                        $preview = Str::limit(strip_tags($body), 80);

                        return $last->user?->name ? "{$last->user->name}: {$preview}" : $preview;
                    }),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('updated_at', direction: 'desc')
            ->defaultPaginationPageOption(20)
            ->filters([
                Filter::make('search')
                    ->form([
                        TextInput::make('search')
                            ->label('Search participants')
                            ->placeholder('Name or email'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $search = $data['search'] ?? '';
                        if (! is_string($search) || trim($search) === '') {
                            return $query;
                        }

                        $term = '%'.addcslashes(trim($search), '%_\\').'%';

                        return $query->whereHas(
                            'participants',
                            fn (Builder $q) => $q->where('name', 'like', $term)->orWhere('email', 'like', $term),
                        );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
