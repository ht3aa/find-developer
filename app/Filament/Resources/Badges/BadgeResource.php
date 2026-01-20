<?php

namespace App\Filament\Resources\Badges;

use App\Filament\Resources\Badges\Pages\CreateBadge;
use App\Filament\Resources\Badges\Pages\EditBadge;
use App\Filament\Resources\Badges\Pages\ListBadges;
use App\Filament\Resources\Badges\Schemas\BadgeForm;
use App\Filament\Resources\Badges\Tables\BadgesTable;
use App\Models\Badge;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BadgeResource extends Resource
{
    protected static ?string $model = Badge::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return 'Badges';
    }

    public static function form(Schema $schema): Schema
    {
        return BadgeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BadgesTable::configure($table);
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
            'index' => ListBadges::route('/'),
            'create' => CreateBadge::route('/create'),
            'edit' => EditBadge::route('/{record}/edit'),
        ];
    }
}
