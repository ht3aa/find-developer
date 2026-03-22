<?php

namespace App\Filament\Resources\Hackathons\Pages;

use App\Filament\Resources\Hackathons\HackathonResource;
use App\Models\Hackathon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateHackathon extends CreateRecord
{
    protected static string $resource = HackathonResource::class;

    protected ?UploadedFile $pendingImageUpload = null;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $image = $data['image'] ?? null;
        unset($data['image']);

        if ($image instanceof UploadedFile) {
            $this->pendingImageUpload = $image;
        }

        $data['slug'] = Str::slug($data['title']);

        if (Hackathon::query()->where('slug', $data['slug'])->exists()) {
            throw ValidationException::withMessages([
                'title' => 'A hackathon with this title already exists.',
            ]);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->pendingImageUpload instanceof UploadedFile) {
            $path = $this->pendingImageUpload->store('hackathons/'.$this->record->getKey(), ['disk' => 's3']);
            $this->record->update(['image' => $path]);
        }
    }
}
