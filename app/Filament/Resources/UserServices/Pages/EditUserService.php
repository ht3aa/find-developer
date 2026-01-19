<?php

namespace App\Filament\Resources\UserServices\Pages;

use App\Filament\Resources\UserServices\UserServiceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUserService extends EditRecord
{
    protected static string $resource = UserServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
