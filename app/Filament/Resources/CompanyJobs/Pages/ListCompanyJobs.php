<?php

namespace App\Filament\Resources\CompanyJobs\Pages;

use App\Filament\Resources\CompanyJobs\CompanyJobResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListCompanyJobs extends ListRecords
{
    protected static string $resource = CompanyJobResource::class;

    public function getSubheading(): string|Htmlable|null
    {
        $card = config('services.qi.card_number');

        return 'Minimum first payment (Qi Card): '.$card.'. Approve only after payment is confirmed.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
