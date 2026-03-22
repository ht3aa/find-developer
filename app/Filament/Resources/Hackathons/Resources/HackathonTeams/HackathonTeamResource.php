<?php

namespace App\Filament\Resources\Hackathons\Resources\HackathonTeams;

use App\Filament\Resources\Hackathons\HackathonResource;
use App\Filament\Resources\Hackathons\Resources\HackathonTeams\Pages\CreateHackathonTeam;
use App\Filament\Resources\Hackathons\Resources\HackathonTeams\Pages\EditHackathonTeam;
use App\Filament\Resources\Hackathons\Resources\HackathonTeams\Pages\ListHackathonTeams;
use App\Filament\Resources\Hackathons\Resources\HackathonTeams\Schemas\HackathonTeamForm;
use App\Filament\Resources\Hackathons\Resources\HackathonTeams\Tables\HackathonTeamsTable;
use App\Models\HackathonTeam;
use BackedEnum;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HackathonTeamResource extends Resource
{
    protected static ?string $model = HackathonTeam::class;

    protected static ?string $slug = 'teams';

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $parentResource = HackathonResource::class;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return HackathonResource::asParent(static::class)
            ->relationship('teams');
    }

    public static function form(Schema $schema): Schema
    {
        return HackathonTeamForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HackathonTeamsTable::configure($table);
    }

    /**
     * @return Builder<HackathonTeam>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount([
            'members',
            'votes as votes_count' => fn ($query) => $query->where('is_voted', true),
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
            'index' => ListHackathonTeams::route('/'),
            'create' => CreateHackathonTeam::route('/create'),
            'edit' => EditHackathonTeam::route('/{record}/edit'),
        ];
    }
}
