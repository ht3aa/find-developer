<?php

namespace App\Filament\Widgets;

use App\Enums\WorldGovernorate;
use App\Models\Skill;
use Filament\Widgets\ChartWidget;

class DevelopersBySkillsChart extends ChartWidget
{
    protected ?string $heading = 'Developers By Skills';

    protected function getData(): array
    {
        $iraqLocations = WorldGovernorate::getIraqLocations();

        $skills = Skill::withCount(['developers' => function ($query) use ($iraqLocations) {
            $query->withoutGlobalScopes()
                ->whereIn('location', $iraqLocations);
        }])
            ->having('developers_count', '>', 0)
            ->orderByDesc('developers_count')
            ->limit(15)
            ->get();

        $labels = [];
        $data = [];

        foreach ($skills as $skill) {
            $labels[] = $skill->name;
            $data[] = $skill->developers_count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Developers',
                    'data' => $data,
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#059669',
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
