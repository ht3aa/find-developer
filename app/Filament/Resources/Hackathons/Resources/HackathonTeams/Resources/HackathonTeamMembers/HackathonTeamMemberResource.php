<?php

namespace App\Filament\Resources\Hackathons\Resources\HackathonTeams\Resources\HackathonTeamMembers;

use App\Filament\Resources\Hackathons\Resources\HackathonTeams\HackathonTeamResource;
use App\Filament\Resources\Hackathons\Resources\HackathonTeams\Resources\HackathonTeamMembers\Pages\CreateHackathonTeamMember;
use App\Filament\Resources\Hackathons\Resources\HackathonTeams\Resources\HackathonTeamMembers\Pages\EditHackathonTeamMember;
use App\Filament\Resources\Hackathons\Resources\HackathonTeams\Resources\HackathonTeamMembers\Pages\ListHackathonTeamMembers;
use App\Filament\Resources\Hackathons\Resources\HackathonTeams\Resources\HackathonTeamMembers\Schemas\HackathonTeamMemberForm;
use App\Filament\Resources\Hackathons\Resources\HackathonTeams\Resources\HackathonTeamMembers\Tables\HackathonTeamMembersTable;
use App\Models\HackathonTeamMember;
use BackedEnum;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HackathonTeamMemberResource extends Resource
{
    protected static ?string $model = HackathonTeamMember::class;

    protected static ?string $slug = 'members';

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static ?string $parentResource = HackathonTeamResource::class;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return HackathonTeamResource::asParent(static::class)
            ->relationship('members');
    }

    public static function form(Schema $schema): Schema
    {
        return HackathonTeamMemberForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HackathonTeamMembersTable::configure($table);
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
            'index' => ListHackathonTeamMembers::route('/'),
            'create' => CreateHackathonTeamMember::route('/create'),
            'edit' => EditHackathonTeamMember::route('/{record}/edit'),
        ];
    }
}
