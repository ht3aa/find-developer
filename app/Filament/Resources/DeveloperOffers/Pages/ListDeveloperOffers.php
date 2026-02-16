<?php

namespace App\Filament\Resources\DeveloperOffers\Pages;

use App\Filament\Resources\DeveloperOffers\DeveloperOfferResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeveloperOffers extends ListRecords
{
    protected static string $resource = DeveloperOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
