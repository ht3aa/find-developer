<?php

namespace App\Filament\Resources\Developers\Pages;

use App\Enums\DeveloperStatus;
use App\Filament\Resources\Developers\DeveloperResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDevelopers extends ListRecords
{
    protected static string $resource = DeveloperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = collect(DeveloperStatus::cases())
            ->mapWithKeys(fn (DeveloperStatus $status) => [
                $status->value => Tab::make($status->getLabel())
                    ->icon($status->getIcon())
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $status)),
            ])
            ->toArray();

        return [
            'all' => Tab::make('All'),
            ...$tabs,
        ];
    }
}
