<?php

namespace App\Filament\Resources\DeveloperRecommendations;

use App\Enums\RecommendationStatus;
use App\Filament\Resources\DeveloperRecommendations\Pages\CreateDeveloperRecommendation;
use App\Filament\Resources\DeveloperRecommendations\Pages\EditDeveloperRecommendation;
use App\Filament\Resources\DeveloperRecommendations\Pages\ListDeveloperRecommendations;
use App\Filament\Resources\DeveloperRecommendations\Pages\ViewDeveloperRecommendation;
use App\Filament\Resources\DeveloperRecommendations\Schemas\DeveloperRecommendationForm;
use App\Filament\Resources\DeveloperRecommendations\Schemas\DeveloperRecommendationInfolist;
use App\Filament\Resources\DeveloperRecommendations\Tables\DeveloperRecommendationsTable;
use App\Models\DeveloperRecommendation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DeveloperRecommendationResource extends Resource
{
    protected static ?string $model = DeveloperRecommendation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Developer Recommendations';

    public static function form(Schema $schema): Schema
    {
        return DeveloperRecommendationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DeveloperRecommendationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeveloperRecommendationsTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        return parent::getEloquentQuery()->where('status', RecommendationStatus::PENDING)->count();
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
            'index' => ListDeveloperRecommendations::route('/'),
            'create' => CreateDeveloperRecommendation::route('/create'),
            'view' => ViewDeveloperRecommendation::route('/{record}'),
            'edit' => EditDeveloperRecommendation::route('/{record}/edit'),
        ];
    }
}
