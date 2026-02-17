<?php

namespace App\Filament\Resources\DeveloperBlogs\Pages;

use App\Filament\Resources\DeveloperBlogs\DeveloperBlogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeveloperBlogs extends ListRecords
{
    protected static string $resource = DeveloperBlogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
