<?php

namespace App\Filament\Resources\DeveloperOffers;

use App\Enums\NavigationGroup;
use App\Filament\Resources\DeveloperOffers\Pages\EditDeveloperOffer;
use App\Filament\Resources\DeveloperOffers\Pages\ListDeveloperOffers;
use App\Filament\Resources\DeveloperOffers\Schemas\DeveloperOfferForm;
use App\Filament\Resources\DeveloperOffers\Tables\DeveloperOffersTable;
use App\Models\DeveloperOffer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DeveloperOfferResource extends Resource
{
    protected static ?string $model = DeveloperOffer::class;

    protected static ?string $slug = 'developer-offers';

    protected static ?string $navigationLabel = 'Developer offers';

    protected static ?string $modelLabel = 'Developer offer';

    protected static ?string $pluralModelLabel = 'Developer offers';

    protected static ?string $recordTitleAttribute = 'company_name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaperAirplane;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Developers->getLabel();
    }

    public static function getNavigationSort(): ?int
    {
        return 23;
    }

    public static function form(Schema $schema): Schema
    {
        return DeveloperOfferForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeveloperOffersTable::configure($table);
    }

    /**
     * @return Builder<DeveloperOffer>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['jobTitle:id,name', 'user:id,name,email']);
    }

    public static function canCreate(): bool
    {
        return false;
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
            'edit' => EditDeveloperOffer::route('/{record}/edit'),
        ];
    }
}
