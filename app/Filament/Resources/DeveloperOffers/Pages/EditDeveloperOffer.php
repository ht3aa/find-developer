<?php

namespace App\Filament\Resources\DeveloperOffers\Pages;

use App\Filament\Resources\DeveloperOffers\DeveloperOfferResource;
use App\Models\DeveloperOffer;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeveloperOffer extends EditRecord
{
    protected static string $resource = DeveloperOfferResource::class;

    /**
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var DeveloperOffer $record */
        $record = $this->record;
        $record->loadMissing(['jobTitle', 'user']);

        $data['developer_names_display'] = $record->developers()->pluck('name')->join(', ');
        $data['job_title_name'] = $record->jobTitle?->name;
        $data['work_type_label'] = $record->work_type?->getLabel();
        $data['submitted_by_display'] = $record->user !== null
            ? "{$record->user->name} ({$record->user->email})"
            : '—';
        $data['created_at_display'] = $record->created_at?->format('Y-m-d H:i');
        $data['updated_at_display'] = $record->updated_at?->format('Y-m-d H:i');

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
