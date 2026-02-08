<?php

namespace App\Filament\Widgets;

use App\Enums\WorldGovernorate;
use App\Models\JobTitle;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class DevelopersByJobTitleChart extends ChartWidget
{
    protected ?string $heading = 'Developers By Job Title';

    protected function getData(): array
    {
        $iraqLocations = WorldGovernorate::getIraqLocations();

        $jobTitles = JobTitle::withCount(['developers' => function ($query) use ($iraqLocations) {
            $query->withoutGlobalScopes()
                ->whereIn('location', $iraqLocations);
        }])
            ->having('developers_count', '>', 0)
            ->orderByDesc('developers_count')
            ->limit(10)
            ->get();

        $labels = [];
        $data = [];
        foreach ($jobTitles as $jobTitle) {
            $labels[] = $jobTitle->name;
            $data[] = $jobTitle->developers_count;
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

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
               onClick: function(e, barElement, instance) {

                let clickedElementIndex = instance.getActiveElements()[0].index;
                let clickedName  = instance.data.labels[clickedElementIndex];
                window.location.href = '/?jobTitles[0]=' + clickedName;
                }
            }
        JS);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
