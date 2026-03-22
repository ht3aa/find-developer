<?php

namespace App\Filament\Resources\Hackathons\Pages;

use App\Filament\Resources\Hackathons\HackathonResource;
use App\Models\Hackathon;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EditHackathon extends EditRecord
{
    protected static string $resource = HackathonResource::class;

    protected ?UploadedFile $pendingImageUpload = null;

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
        $image = $data['image'] ?? null;
        unset($data['image']);

        if ($image instanceof UploadedFile) {
            $this->pendingImageUpload = $image;
        }

        $data['slug'] = Str::slug($data['title']);

        if (Hackathon::query()->where('slug', $data['slug'])->whereKeyNot($this->record->getKey())->exists()) {
            throw ValidationException::withMessages([
                'title' => 'A hackathon with this title already exists.',
            ]);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->pendingImageUpload instanceof UploadedFile) {
            if ($this->record->image) {
                Storage::disk('s3')->delete($this->record->image);
            }

            $path = $this->pendingImageUpload->store('hackathons/'.$this->record->getKey(), ['disk' => 's3']);
            $this->record->update(['image' => $path]);
        }
    }
}
