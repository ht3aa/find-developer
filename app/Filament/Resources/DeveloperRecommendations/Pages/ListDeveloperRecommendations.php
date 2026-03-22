<?php

namespace App\Filament\Resources\DeveloperRecommendations\Pages;

use App\Filament\Resources\DeveloperRecommendations\DeveloperRecommendationResource;
use Filament\Resources\Pages\ListRecords;

class ListDeveloperRecommendations extends ListRecords
{
    protected static string $resource = DeveloperRecommendationResource::class;
}
