<?php

namespace App\Filament\Resources\Messages\RelationManagers;

use App\Filament\Resources\MessageAttachments\MessageAttachmentResource;
use App\Models\MessageAttachment;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Number;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $title = 'Attachments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_name')
            ->defaultSort('created_at', direction: 'desc')
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('file_name')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('file_type')
                    ->toggleable(),
                TextColumn::make('file_size')
                    ->label('Size')
                    ->formatStateUsing(fn (?int $state): string => $state !== null
                        ? Number::fileSize($state)
                        : '—'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (MessageAttachment $record): string => MessageAttachmentResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
