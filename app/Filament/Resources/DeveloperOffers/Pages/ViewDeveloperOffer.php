<?php

namespace App\Filament\Resources\DeveloperOffers\Pages;

use App\Filament\Resources\DeveloperOffers\DeveloperOfferResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDeveloperOffer extends ViewRecord
{
    protected static string $resource = DeveloperOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
