<?php

namespace App\Filament\Resources\Badges\Pages;

use App\Filament\Resources\Badges\BadgeResource;
use App\Models\Badge;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateBadge extends CreateRecord
{
    protected static string $resource = BadgeResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::slug($data['name']);

        if (Badge::where('slug', $data['slug'])->exists()) {
            throw ValidationException::withMessages([
                'data.name' => 'Badge with this name already exists.',
            ]);
        }

        return $data;
    }
}
