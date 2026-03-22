<?php

namespace App\Filament\Resources\MessageAttachments\Tables;

use App\Models\MessageAttachment;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class MessageAttachmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('message_id')
                    ->label('Message')
                    ->sortable(),
                TextColumn::make('file_name')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('file_type')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('file_size')
                    ->label('Size')
                    ->formatStateUsing(fn (?int $state): string => $state !== null
                        ? Number::fileSize($state)
                        : '—'),
                TextColumn::make('sender_name')
                    ->label('Sender')
                    ->state(fn (MessageAttachment $record): string => $record->message?->user?->name ?? '—'),
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
                            ->placeholder('File name or type'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $search = $data['search'] ?? '';
                        if (! is_string($search) || trim($search) === '') {
                            return $query;
                        }

                        $term = '%'.addcslashes(trim($search), '%_\\').'%';

                        return $query->where(function (Builder $q) use ($term): void {
                            $q->where('file_name', 'like', $term)
                                ->orWhere('file_type', 'like', $term);
                        });
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
