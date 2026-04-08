<?php

namespace App\Services;

use App\Enums\AvailabilityType;
use App\Enums\Currency;
use App\Models\Developer;
use Illuminate\Support\Collection;

class DeveloperChartDataService
{
    /**
     * @return array<int, array{label: string, count: int}>
     */
    public function developersByLocation(): array
    {
        return Developer::query()
            ->get()
            ->groupBy(fn (Developer $d) => $d->location?->getLabel() ?? 'Unknown')
            ->map(fn (Collection $group, string $label) => ['label' => $label, 'count' => $group->count()])
            ->sortDesc()
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{label: string, count: int}>
     */
    public function developersByAvailabilityType(): array
    {
        $counts = Developer::query()
            ->get()
            ->pluck('availability_type')
            ->filter()
            ->flatten()
            ->map(fn ($type) => $type instanceof AvailabilityType ? $type->getLabel() : (string) $type)
            ->countBy();

        return collect(AvailabilityType::cases())
            ->map(fn (AvailabilityType $type) => [
                'label' => $type->getLabel(),
                'count' => $counts->get($type->getLabel(), 0),
            ])
            ->filter(fn (array $row) => $row['count'] > 0)
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{years_of_experience: int, average_salary: float}>
     */
    public function averageSalaryByExperience(): array
    {
        return Developer::query()
            ->where('salary_currency', Currency::IQD)
            ->whereNotNull('expected_salary_from')
            ->whereNotNull('expected_salary_to')
            ->get()
            ->groupBy('years_of_experience')
            ->map(fn (Collection $group, int|string $years) => [
                'years_of_experience' => (int) $years,
                'average_salary' => round($group->avg(fn (Developer $d) => ($d->expected_salary_from + $d->expected_salary_to) / 2), 0),
            ])
            ->sortBy('years_of_experience')
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{label: string, count: int}>
     */
    public function developersByJobTitle(): array
    {
        return Developer::query()
            ->with('jobTitle')
            ->get()
            ->groupBy(fn (Developer $d) => $d->jobTitle?->name ?? 'Unknown')
            ->map(fn (Collection $group, string $label) => ['label' => $label, 'count' => $group->count()])
            ->sortDesc()
            ->values()
            ->all();
    }
}
