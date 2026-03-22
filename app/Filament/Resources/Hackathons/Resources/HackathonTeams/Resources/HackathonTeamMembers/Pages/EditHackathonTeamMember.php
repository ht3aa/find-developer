<?php

namespace App\Filament\Resources\Hackathons\Resources\HackathonTeams\Resources\HackathonTeamMembers\Pages;

use App\Filament\Resources\Hackathons\Resources\HackathonTeams\Resources\HackathonTeamMembers\HackathonTeamMemberResource;
use App\Models\HackathonTeamMember;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditHackathonTeamMember extends EditRecord
{
    protected static string $resource = HackathonTeamMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $exists = HackathonTeamMember::query()
            ->where('hackathon_team_id', $this->record->hackathon_team_id)
            ->where('developer_id', $data['developer_id'])
            ->whereKeyNot($this->record->getKey())
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'developer_id' => 'This developer is already a member of this team.',
            ]);
        }

        return $data;
    }
}
