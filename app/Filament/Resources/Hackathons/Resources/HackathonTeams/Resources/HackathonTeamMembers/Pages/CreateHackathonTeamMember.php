<?php

namespace App\Filament\Resources\Hackathons\Resources\HackathonTeams\Resources\HackathonTeamMembers\Pages;

use App\Filament\Resources\Hackathons\Resources\HackathonTeams\Resources\HackathonTeamMembers\HackathonTeamMemberResource;
use App\Models\HackathonTeamMember;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateHackathonTeamMember extends CreateRecord
{
    protected static string $resource = HackathonTeamMemberResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['hackathon_team_id'] = $this->getParentRecord()->getKey();

        $exists = HackathonTeamMember::query()
            ->where('hackathon_team_id', $data['hackathon_team_id'])
            ->where('developer_id', $data['developer_id'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'developer_id' => 'This developer is already a member of this team.',
            ]);
        }

        return $data;
    }
}
