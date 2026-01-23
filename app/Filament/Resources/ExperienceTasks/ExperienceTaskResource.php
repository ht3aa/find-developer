<?php

namespace App\Filament\Resources\ExperienceTasks;

use App\Filament\Resources\ExperienceTasks\Pages\CreateExperienceTask;
use App\Filament\Resources\ExperienceTasks\Pages\EditExperienceTask;
use App\Filament\Resources\ExperienceTasks\Pages\ListExperienceTasks;
use App\Filament\Resources\ExperienceTasks\RelationManagers\AssignedDevelopersRelationManager;
use App\Filament\Resources\ExperienceTasks\Schemas\ExperienceTaskForm;
use App\Filament\Resources\ExperienceTasks\Tables\ExperienceTasksTable;
use App\Models\ExperienceTask;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExperienceTaskResource extends Resource
{
    protected static ?string $model = ExperienceTask::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Get Experience';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return ExperienceTaskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExperienceTasksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AssignedDevelopersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExperienceTasks::route('/'),
            'create' => CreateExperienceTask::route('/create'),
            'edit' => EditExperienceTask::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Experience Task';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Experience Tasks';
    }
}
