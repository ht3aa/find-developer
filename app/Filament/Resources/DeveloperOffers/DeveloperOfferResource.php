<?php

namespace App\Filament\Resources\DeveloperOffers;

use App\Enums\OfferStatus;
use App\Filament\Resources\DeveloperOffers\Pages\CreateDeveloperOffer;
use App\Filament\Resources\DeveloperOffers\Pages\EditDeveloperOffer;
use App\Filament\Resources\DeveloperOffers\Pages\ListDeveloperOffers;
use App\Filament\Resources\DeveloperOffers\Pages\ViewDeveloperOffer;
use App\Filament\Resources\DeveloperOffers\Schemas\DeveloperOfferForm;
use App\Filament\Resources\DeveloperOffers\Schemas\DeveloperOfferInfolist;
use App\Filament\Resources\DeveloperOffers\Tables\DeveloperOffersTable;
use App\Models\DeveloperOffer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DeveloperOfferResource extends Resource
{
    protected static ?string $model = DeveloperOffer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Developer Offers';

    public static function form(Schema $schema): Schema
    {
        return DeveloperOfferForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DeveloperOfferInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeveloperOffersTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        return parent::getEloquentQuery()->where('status', OfferStatus::PENDING)->count();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeveloperOffers::route('/'),
            'create' => CreateDeveloperOffer::route('/create'),
            'view' => ViewDeveloperOffer::route('/{record}'),
            'edit' => EditDeveloperOffer::route('/{record}/edit'),
        ];
    }
}
