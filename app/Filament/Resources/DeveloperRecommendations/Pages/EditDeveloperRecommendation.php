<?php

namespace App\Filament\Resources\DeveloperRecommendations\Pages;

use App\Filament\Resources\DeveloperRecommendations\DeveloperRecommendationResource;
use App\Models\DeveloperRecommendation;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeveloperRecommendation extends EditRecord
{
    protected static string $resource = DeveloperRecommendationResource::class;

    /**
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var DeveloperRecommendation $record */
        $record = $this->record;
        $record->loadMissing(['recommender', 'recommended']);

        $data['recommender_name'] = $record->recommender?->name;
        $data['recommended_name'] = $record->recommended?->name;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
