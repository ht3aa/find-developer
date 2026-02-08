<?php

namespace App\Filament\Widgets;

use App\Enums\WorldGovernorate;
use App\Models\Developer;
use Filament\Widgets\ChartWidget;

class DevelopersByLocationChart extends ChartWidget
{
    protected ?string $heading = 'Developers By Location';

    protected function getData(): array
    {
        $iraqLocations = array_map(fn ($location) => $location->value, WorldGovernorate::getIraqLocations());

        $locations = Developer::withoutGlobalScopes()
            ->selectRaw('location, COUNT(*) as count')
            ->whereNotNull('location')
            ->whereIn('location', $iraqLocations)
            ->groupBy('location')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $labels = [];
        $data = [];

        foreach ($locations as $location) {
            $labels[] = $location->location?->getLabel() ?? 'Unknown';
            $data[] = $location->count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Developers',
                    'data' => $data,
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#2563eb',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
