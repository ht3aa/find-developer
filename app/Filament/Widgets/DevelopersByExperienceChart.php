<?php

namespace App\Filament\Widgets;

use App\Enums\WorldGovernorate;
use App\Models\Developer;
use Filament\Widgets\ChartWidget;

class DevelopersByExperienceChart extends ChartWidget
{
    protected ?string $heading = 'Developers By Experience Range';

    protected function getData(): array
    {
        $iraqLocations = WorldGovernorate::getIraqLocations();
        $ranges = [
            '0-2 years' => [0, 2],
            '3-5 years' => [3, 5],
            '6-10 years' => [6, 10],
            '11-15 years' => [11, 15],
            '16+ years' => [16, 100],
        ];

        $labels = [];
        $data = [];

        foreach ($ranges as $label => $range) {
            $count = Developer::withoutGlobalScopes()
                ->whereIn('location', $iraqLocations)
                ->whereBetween('years_of_experience', $range)
                ->count();

            $labels[] = $label;
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Developers',
                    'data' => $data,
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#059669',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
