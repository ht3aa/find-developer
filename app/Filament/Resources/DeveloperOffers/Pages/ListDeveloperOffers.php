<?php

namespace App\Filament\Resources\DeveloperOffers\Pages;

use App\Filament\Resources\DeveloperOffers\DeveloperOfferResource;
use Filament\Resources\Pages\ListRecords;

class ListDeveloperOffers extends ListRecords
{
    protected static string $resource = DeveloperOfferResource::class;
}
