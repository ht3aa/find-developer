<?php

namespace App\Filament\Resources\DeveloperOffers\Pages;

use App\Filament\Resources\DeveloperOffers\DeveloperOfferResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDeveloperOffer extends EditRecord
{
    protected static string $resource = DeveloperOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
