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
        return [
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', DeveloperStatus::PENDING)
                ),

            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', DeveloperStatus::APPROVED)
                ),

            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', DeveloperStatus::REJECTED)
                ),

            'all' => Tab::make('all'),
        ];
    }
}
