<?php

namespace App\Filament\Resources\DeveloperCompanies;

use App\Enums\NavigationGroup;
use App\Filament\Resources\DeveloperCompanies\Pages\CreateDeveloperCompany;
use App\Filament\Resources\DeveloperCompanies\Pages\EditDeveloperCompany;
use App\Filament\Resources\DeveloperCompanies\Pages\ListDeveloperCompanies;
use App\Filament\Resources\DeveloperCompanies\Schemas\DeveloperCompanyForm;
use App\Filament\Resources\DeveloperCompanies\Tables\DeveloperCompaniesTable;
use App\Models\DeveloperCompany;
use App\Models\Scopes\DeveloperScope;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DeveloperCompanyResource extends Resource
{
    protected static ?string $model = DeveloperCompany::class;

    protected static ?string $slug = 'developer-work-experience';

    protected static ?string $navigationLabel = 'Work experience';

    protected static ?string $modelLabel = 'Work experience';

    protected static ?string $pluralModelLabel = 'Work experiences';

    protected static ?string $recordTitleAttribute = 'company_name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Developers->getLabel();
    }

    public static function getNavigationSort(): ?int
    {
        return 24;
    }

    public static function form(Schema $schema): Schema
    {
        return DeveloperCompanyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeveloperCompaniesTable::configure($table);
    }

    /**
     * @return Builder<DeveloperCompany>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScope(DeveloperScope::class)
            ->with([
                'developer:id,name,slug',
                'jobTitle:id,name',
                'parent:id,company_name,job_title_id',
                'parent.jobTitle:id,name',
            ]);
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
