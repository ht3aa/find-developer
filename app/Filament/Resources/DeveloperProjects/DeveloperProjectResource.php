<?php

namespace App\Filament\Resources\DeveloperProjects;

use App\Filament\Resources\DeveloperProjects\Pages\CreateDeveloperProject;
use App\Filament\Resources\DeveloperProjects\Pages\EditDeveloperProject;
use App\Filament\Resources\DeveloperProjects\Pages\ListDeveloperProjects;
use App\Filament\Resources\DeveloperProjects\Schemas\DeveloperProjectForm;
use App\Filament\Resources\DeveloperProjects\Tables\DeveloperProjectsTable;
use App\Models\DeveloperProject;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DeveloperProjectResource extends Resource
{
    protected static ?string $model = DeveloperProject::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return DeveloperProjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeveloperProjectsTable::configure($table);
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
            'index' => ListDeveloperProjects::route('/'),
            'create' => CreateDeveloperProject::route('/create'),
            'edit' => EditDeveloperProject::route('/{record}/edit'),
        ];
    }
}
