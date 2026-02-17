<?php

namespace App\Filament\Resources\DeveloperBlogs\Pages;

use App\Enums\BlogStatus;
use App\Filament\Resources\DeveloperBlogs\DeveloperBlogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDeveloperBlog extends CreateRecord
{
    protected static string $resource = DeveloperBlogResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->user()->isDeveloper()) {
            $data['developer_id'] = auth()->user()->developer?->id;
        }

        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['title']);
        }

        $data['status'] = BlogStatus::DRAFT->value;

        return $data;
    }
}
