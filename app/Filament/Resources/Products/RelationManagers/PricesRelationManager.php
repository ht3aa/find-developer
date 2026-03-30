<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Enums\Currency;
use App\Models\ProductPrice;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Number;

class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    protected static ?string $title = 'Prices';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->required(),
                Select::make('currency')
                    ->label('Currency')
                    ->options(Currency::class)
                    ->default(Currency::IQD->value)
                    ->required(),
                Toggle::make('is_old_price')
                    ->label('Old price (e.g. strikethrough)')
                    ->helperText('Mark this row as the previous/compare price when showing a sale.')
                    ->default(false),
                Toggle::make('is_new_price')
                    ->label('New / current price')
                    ->helperText('Mark this row as the current selling price.')
                    ->default(true),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('price')
            ->columns([
                TextColumn::make('price')
                    ->label('Price')
                    ->formatStateUsing(function (mixed $state, ProductPrice $record): string {
                        return Number::currency((float) $record->price, $record->currency->value);
                    })
                    ->sortable(),
                TextColumn::make('currency')
                    ->badge()
                    ->sortable(),
                IconColumn::make('is_old_price')
                    ->boolean(),
                IconColumn::make('is_new_price')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
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
