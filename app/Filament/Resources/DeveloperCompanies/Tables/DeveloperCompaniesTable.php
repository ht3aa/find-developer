<?php

namespace App\Filament\Resources\DeveloperCompanies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DeveloperCompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('developer.name')
                    ->label('Developer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jobTitle.name')
                    ->label('Job title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parent.company_name')
                    ->label('Promotion from')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->placeholder('—')
                    ->sortable(),
                IconColumn::make('is_current')
                    ->label('Current')
                    ->boolean(),
                IconColumn::make('show_company')
                    ->label('Public')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('start_date', direction: 'desc')
            ->defaultPaginationPageOption(15)
            ->filters([
                //
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
