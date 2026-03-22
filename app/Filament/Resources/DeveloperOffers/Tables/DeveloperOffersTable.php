<?php

namespace App\Filament\Resources\DeveloperOffers\Tables;

use App\Enums\OfferStatus;
use App\Models\DeveloperOffer;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DeveloperOffersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('developers_display')
                    ->label('Developers')
                    ->state(fn (DeveloperOffer $record): string => $record->developers()->pluck('name')->join(', ')),
                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jobTitle.name')
                    ->label('Job title')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?OfferStatus $state): string => $state?->getLabel() ?? '')
                    ->color(fn (?OfferStatus $state): string => $state?->getColor() ?? 'gray')
                    ->sortable(),
                TextColumn::make('contact_email')
                    ->label('Contact')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', direction: 'desc')
            ->defaultPaginationPageOption(15)
            ->filters([
                SelectFilter::make('status')
                    ->options(OfferStatus::class),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
