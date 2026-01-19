<?php

namespace App\Filament\Resources\UserServices\Pages;

use App\Filament\Resources\UserServices\UserServiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserService extends CreateRecord
{
    protected static string $resource = UserServiceResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!auth()->user()->isSuperAdmin()) {
            $data['user_id'] = auth()->id();
        }

        return $data;
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
