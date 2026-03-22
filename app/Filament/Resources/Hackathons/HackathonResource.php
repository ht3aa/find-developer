<?php

namespace App\Filament\Resources\Hackathons;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Hackathons\Pages\CreateHackathon;
use App\Filament\Resources\Hackathons\Pages\EditHackathon;
use App\Filament\Resources\Hackathons\Pages\ListHackathons;
use App\Filament\Resources\Hackathons\RelationManagers\AttendancesRelationManager;
use App\Filament\Resources\Hackathons\RelationManagers\SubscribersRelationManager;
use App\Filament\Resources\Hackathons\Schemas\HackathonForm;
use App\Filament\Resources\Hackathons\Tables\HackathonsTable;
use App\Models\Hackathon;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HackathonResource extends Resource
{
    protected static ?string $model = Hackathon::class;

    protected static ?string $slug = 'hackathons';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'Hackathons';

    protected static ?string $modelLabel = 'Hackathon';

    protected static ?string $pluralModelLabel = 'Hackathons';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Developers->getLabel();
    }

    public static function getNavigationSort(): ?int
    {
        return 30;
    }

    public static function form(Schema $schema): Schema
    {
        return HackathonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HackathonsTable::configure($table);
    }

    /**
     * @return Builder<Hackathon>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['rewardBadge']);
    }

    public static function getRelations(): array
    {
        return [
            SubscribersRelationManager::class,
            AttendancesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHackathons::route('/'),
            'create' => CreateHackathon::route('/create'),
            'edit' => EditHackathon::route('/{record}/edit'),
        ];
    }
}
