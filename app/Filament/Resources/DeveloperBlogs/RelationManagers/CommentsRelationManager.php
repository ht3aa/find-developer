<?php

namespace App\Filament\Resources\DeveloperBlogs\RelationManagers;

use App\Enums\BlogCommentStatus;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('body')
                    ->limit(60)
                    ->wrap()
                    ->tooltip(fn ($record) => $record->body),

                Tables\Columns\TextColumn::make('parent_id')
                    ->label('Reply to')
                    ->formatStateUsing(fn ($state, $record) => $record->parent_id ? '#' . $record->parent_id : '—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ActionGroup::make([
                    Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn ($record) => $record->status !== BlogCommentStatus::APPROVED)
                        ->action(fn ($record) => $record->update(['status' => BlogCommentStatus::APPROVED])),

                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn ($record) => $record->status !== BlogCommentStatus::REJECTED)
                        ->action(fn ($record) => $record->update(['status' => BlogCommentStatus::REJECTED])),

                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label('Approve selected')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn (Collection $records) => $records->each->update(['status' => BlogCommentStatus::APPROVED])),

                    BulkAction::make('reject')
                        ->label('Reject selected')
                        ->icon('heroicon-o-x-circle')
                        ->action(fn (Collection $records) => $records->each->update(['status' => BlogCommentStatus::REJECTED])),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
