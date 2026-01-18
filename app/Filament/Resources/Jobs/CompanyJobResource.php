<?php

namespace App\Filament\Resources\Jobs;

use App\Filament\Resources\Jobs\Pages\CreateCompanyJob;
use App\Filament\Resources\Jobs\Pages\EditCompanyJob;
use App\Filament\Resources\Jobs\Pages\ListCompanyJobs;
use App\Filament\Resources\Jobs\Schemas\CompanyJobForm;
use App\Filament\Resources\Jobs\Tables\CompanyJobsTable;
use App\Models\CompanyJob;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CompanyJobResource extends Resource
{
    protected static ?string $model = CompanyJob::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return CompanyJobForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompanyJobsTable::configure($table);
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
            'index' => ListCompanyJobs::route('/'),
            'create' => CreateCompanyJob::route('/create'),
            'edit' => EditCompanyJob::route('/{record}/edit'),
        ];
    }
}
