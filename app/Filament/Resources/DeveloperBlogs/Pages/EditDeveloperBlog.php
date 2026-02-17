<?php

namespace App\Filament\Resources\DeveloperBlogs\Pages;

use App\Enums\BlogStatus;
use App\Filament\Resources\DeveloperBlogs\DeveloperBlogResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeveloperBlog extends EditRecord
{
    protected static string $resource = DeveloperBlogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Prevent non-admin users from changing status using policy
        if (auth()->user()->isSuperAdmin() && $data['status'] === BlogStatus::PUBLISHED) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
