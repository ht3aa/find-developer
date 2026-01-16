<?php

namespace App\Filament\Resources\Developers;

use App\Filament\Resources\Developers\Pages\CreateDeveloper;
use App\Filament\Resources\Developers\Pages\EditDeveloper;
use App\Filament\Resources\Developers\Pages\ListDevelopers;
use App\Filament\Resources\Developers\RelationManagers\ProjectsRelationManager;
use App\Filament\Resources\Developers\Schemas\DeveloperForm;
use App\Filament\Resources\Developers\Tables\DevelopersTable;
use App\Models\Developer;
use App\Models\Scopes\ApprovedScope;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DeveloperResource extends Resource
{
    protected static ?string $model = Developer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return DeveloperForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DevelopersTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            ApprovedScope::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            ProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDevelopers::route('/'),
            'create' => CreateDeveloper::route('/create'),
            'edit' => EditDeveloper::route('/{record}/edit'),
        ];
    }
}
