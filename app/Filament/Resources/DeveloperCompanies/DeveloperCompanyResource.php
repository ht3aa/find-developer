<?php

namespace App\Filament\Resources\DeveloperCompanies;

use App\Filament\Resources\DeveloperCompanies\Pages\CreateDeveloperCompany;
use App\Filament\Resources\DeveloperCompanies\Pages\EditDeveloperCompany;
use App\Filament\Resources\DeveloperCompanies\Pages\ListDeveloperCompanies;
use App\Filament\Resources\DeveloperCompanies\Schemas\DeveloperCompanyForm;
use App\Filament\Resources\DeveloperCompanies\Tables\DeveloperCompaniesTable;
use App\Models\DeveloperCompany;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DeveloperCompanyResource extends Resource
{
    protected static ?string $model = DeveloperCompany::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Developer Companies';

    protected static ?string $modelLabel = 'Developer Company';

    protected static ?string $pluralModelLabel = 'Developer Companies';

    public static function form(Schema $schema): Schema
    {
        return DeveloperCompanyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeveloperCompaniesTable::configure($table);
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
            'index' => ListDeveloperCompanies::route('/'),
            'create' => CreateDeveloperCompany::route('/create'),
            'edit' => EditDeveloperCompany::route('/{record}/edit'),
        ];
    }
}
