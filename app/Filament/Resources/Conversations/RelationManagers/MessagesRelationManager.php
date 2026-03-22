<?php

namespace App\Filament\Resources\Conversations\RelationManagers;

use App\Filament\Resources\Messages\MessageResource;
use App\Models\Message;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    protected static ?string $title = 'Messages';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('created_at', direction: 'desc')
            ->defaultPaginationPageOption(50)
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Sender')
                    ->searchable(),
                TextColumn::make('body')
                    ->label('Preview')
                    ->formatStateUsing(fn (?string $state): string => Str::limit(strip_tags((string) $state), 100))
                    ->wrap(),
                TextColumn::make('attachments_count')
                    ->counts('attachments')
                    ->label('Files'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Message $record): string => MessageResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
