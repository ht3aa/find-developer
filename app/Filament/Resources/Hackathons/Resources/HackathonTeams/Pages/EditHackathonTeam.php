<?php

namespace App\Filament\Resources\Hackathons\Resources\HackathonTeams\Pages;

use App\Filament\Resources\Hackathons\Resources\HackathonTeams\HackathonTeamResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EditHackathonTeam extends EditRecord
{
    protected static string $resource = HackathonTeamResource::class;

    protected ?UploadedFile $pendingLogoUpload = null;

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
        $logo = $data['logo'] ?? null;
        unset($data['logo']);

        if ($logo instanceof UploadedFile) {
            $this->pendingLogoUpload = $logo;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->pendingLogoUpload instanceof UploadedFile) {
            if ($this->record->logo) {
                Storage::disk('s3')->delete($this->record->logo);
            }

            $path = $this->pendingLogoUpload->store('hackathon-teams/'.$this->record->getKey(), ['disk' => 's3']);
            $this->record->update(['logo' => $path]);
        }
    }
}
