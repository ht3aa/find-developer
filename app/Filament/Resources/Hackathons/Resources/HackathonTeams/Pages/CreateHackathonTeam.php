<?php

namespace App\Filament\Resources\Hackathons\Resources\HackathonTeams\Pages;

use App\Filament\Resources\Hackathons\Resources\HackathonTeams\HackathonTeamResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\UploadedFile;

class CreateHackathonTeam extends CreateRecord
{
    protected static string $resource = HackathonTeamResource::class;

    protected ?UploadedFile $pendingLogoUpload = null;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $logo = $data['logo'] ?? null;
        unset($data['logo']);

        if ($logo instanceof UploadedFile) {
            $this->pendingLogoUpload = $logo;
        }

        $data['hackathon_id'] = $this->getParentRecord()->getKey();

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->pendingLogoUpload instanceof UploadedFile) {
            $path = $this->pendingLogoUpload->store('hackathon-teams/'.$this->record->getKey(), ['disk' => 's3']);
            $this->record->update(['logo' => $path]);
        }
    }
}
