<?php

namespace App\Filament\Widgets;

use App\Enums\AvailabilityType;
use App\Enums\WorldGovernorate;
use App\Models\Developer;
use Filament\Widgets\ChartWidget;

class DevelopersByAvailabilityTypeChart extends ChartWidget
{
    protected ?string $heading = 'Developers By Availability Type';

    protected function getData(): array
    {
        $iraqLocations = array_map(fn($location) => $location->value, WorldGovernorate::getIraqLocations());
        $types = AvailabilityType::cases();
        $labels = [];
        $data = [];
        $colors = [];

        foreach ($types as $type) {
            $count = Developer::withoutGlobalScopes()
                ->whereIn('location', $iraqLocations)
                ->whereJsonContains('availability_type', $type->value)
                ->count();

            $labels[] = $type->getLabel();
            $data[] = $count;
            $colors[] = $type->getHexColor();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Developers',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
