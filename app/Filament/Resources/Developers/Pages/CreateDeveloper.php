<?php

namespace App\Filament\Resources\Developers\Pages;

use App\Enums\DeveloperStatus;
use App\Filament\Resources\Developers\DeveloperResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateDeveloper extends CreateRecord
{
    protected static string $resource = DeveloperResource::class;

    /** @var array<int|string>|null */
    protected ?array $skillIdsToSync = null;

    /** @var array<int|string>|null */
    protected ?array $badgeIdsToSync = null;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['user_id'])) {
            throw ValidationException::withMessages([
                'data.user_id' => 'A user account is required to create a developer profile.',
            ]);
        }

        $this->skillIdsToSync = isset($data['skills']) && is_array($data['skills']) ? $data['skills'] : null;
        $this->badgeIdsToSync = isset($data['badges']) && is_array($data['badges']) ? $data['badges'] : null;
        unset($data['skills'], $data['badges']);

        $data['slug'] = Str::slug($data['name']);
        $data['status'] = $data['status'] ?? DeveloperStatus::PENDING;
        $data['is_available'] = $data['is_available'] ?? false;
        $data['recommended_by_us'] = $data['recommended_by_us'] ?? false;

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->getRecord();

        if ($this->skillIdsToSync !== null) {
            $record->skills()->sync($this->skillIdsToSync);
        }

        if ($this->badgeIdsToSync !== null) {
            $record->badges()->sync($this->badgeIdsToSync);
        }
    }
}
