<?php

namespace App\Filament\Resources\DeveloperRecommendations;

use App\Enums\NavigationGroup;
use App\Filament\Resources\DeveloperRecommendations\Pages\EditDeveloperRecommendation;
use App\Filament\Resources\DeveloperRecommendations\Pages\ListDeveloperRecommendations;
use App\Filament\Resources\DeveloperRecommendations\Schemas\DeveloperRecommendationForm;
use App\Filament\Resources\DeveloperRecommendations\Tables\DeveloperRecommendationsTable;
use App\Models\DeveloperRecommendation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DeveloperRecommendationResource extends Resource
{
    protected static ?string $model = DeveloperRecommendation::class;

    protected static ?string $slug = 'developer-recommendations';

    protected static ?string $navigationLabel = 'Recommendations';

    protected static ?string $modelLabel = 'Developer recommendation';

    protected static ?string $pluralModelLabel = 'Developer recommendations';

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHandThumbUp;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Developers->getLabel();
    }

    public static function getNavigationSort(): ?int
    {
        return 22;
    }

    public static function form(Schema $schema): Schema
    {
        return DeveloperRecommendationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeveloperRecommendationsTable::configure($table);
    }

    /**
     * @return Builder<DeveloperRecommendation>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['recommender', 'recommended']);
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
            'index' => ListDeveloperRecommendations::route('/'),
            'edit' => EditDeveloperRecommendation::route('/{record}/edit'),
        ];
    }
}
