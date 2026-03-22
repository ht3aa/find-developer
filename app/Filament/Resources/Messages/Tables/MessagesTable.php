<?php

namespace App\Filament\Resources\Messages\Tables;

use App\Models\Message;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class MessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('conversation_id')
                    ->label('Conversation')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Sender')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('body_preview')
                    ->label('Preview')
                    ->state(fn (Message $m): string => Str::limit(strip_tags((string) $m->body), 100)),
                TextColumn::make('attachments_count')
                    ->counts('attachments')
                    ->label('Files'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', direction: 'desc')
            ->defaultPaginationPageOption(20)
            ->filters([
                Filter::make('search')
                    ->form([
                        TextInput::make('search')
                            ->label('Search')
                            ->placeholder('Message body, sender name or email'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $search = $data['search'] ?? '';
                        if (! is_string($search) || trim($search) === '') {
                            return $query;
                        }

                        $term = '%'.addcslashes(trim($search), '%_\\').'%';

                        return $query->where(function (Builder $q) use ($term): void {
                            $q->where('body', 'like', $term)
                                ->orWhereHas(
                                    'user',
                                    fn (Builder $sub) => $sub->where('name', 'like', $term)->orWhere('email', 'like', $term),
                                );
                        });
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
