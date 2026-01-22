<?php

namespace App\Filament\Widgets;

use App\Enums\WorldGovernorate;
use App\Models\Developer;
use Filament\Widgets\ChartWidget;

class AverageSalaryByExperienceChart extends ChartWidget
{
    protected ?string $heading = 'Average Salary By Experience Range';

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
            $developers = Developer::withoutGlobalScopes()
                ->whereIn('location', $iraqLocations)
                ->whereBetween('years_of_experience', $range)
                ->whereNotNull('expected_salary_from')
                ->get();

            $salaries = $developers->map(function ($developer) {
                // Calculate midpoint of salary range, or use from if to is null
                if ($developer->expected_salary_to) {
                    return ($developer->expected_salary_from + $developer->expected_salary_to) / 2;
                }
                return $developer->expected_salary_from;
            })->filter();

            $averageSalary = $salaries->isNotEmpty()
                ? round($salaries->average(), 2)
                : 0;

            $labels[] = $label;
            $data[] = $averageSalary;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Average Salary',
                    'data' => $data,
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#2563eb',
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
