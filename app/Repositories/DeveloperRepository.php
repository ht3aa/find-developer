<?php

namespace App\Repositories;

use App\Models\Developer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DeveloperRepository
{
    /**
     * Get paginated developers with optional search via query string.
     */
    public function getPaginated(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(Developer::class)
            ->with([
                'jobTitle',
                'skills',
                'badges',
            ])
            ->withCount('recommendationsReceived')
            ->withCount('badges')
            ->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::callback('job_title.name', function ($query, $value) {
                    $values = $this->parseFilterValues($value);
                    if (empty($values)) {
                        return;
                    }
                    $query->whereHas('jobTitle', function ($q) use ($values) {
                        $q->where(function ($q) use ($values) {
                            foreach ($values as $val) {
                                $term = '%'.addcslashes($val, '%_').'%';
                                $q->orWhere('name', 'like', $term);
                            }
                        });
                    });
                }),
                AllowedFilter::callback('search', function ($query, $value) {
                    if (blank($value)) {
                        return;
                    }
                    $term = '%'.addcslashes($value, '%_').'%';
                    $query->where(function ($query) use ($term) {
                        $query->where('developers.name', 'like', $term)
                            ->orWhere('developers.email', 'like', $term)
                            ->orWhereHas('skills', function ($q) use ($term) {
                                $q->where('skills.name', 'like', $term);
                            });
                    });
                }),
                AllowedFilter::callback('skill', function ($query, $value) {
                    $values = $this->parseFilterValues($value);
                    if (empty($values)) {
                        return;
                    }
                    $query->whereHas('skills', function ($q) use ($values) {
                        $q->where(function ($q) use ($values) {
                            foreach ($values as $val) {
                                $term = '%'.addcslashes($val, '%_').'%';
                                $q->orWhere('skills.name', 'like', $term);
                            }
                        });
                    });
                }),
                AllowedFilter::callback('years_min', function ($query, $value) {
                    if ($value === null || $value === '') {
                        return;
                    }
                    $query->where('years_of_experience', '>=', (int) $value);
                }),
                AllowedFilter::callback('years_max', function ($query, $value) {
                    if ($value === null || $value === '') {
                        return;
                    }
                    $query->where('years_of_experience', '<=', (int) $value);
                }),
            ])
            ->allowedSorts(['name', 'years_of_experience', 'created_at'])
            ->orderBy('badges_count', 'desc')
            ->orderBy('recommended_by_us', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get count of recommended developers matching the current filters.
     */
    public function getRecommendedCount(Request $request): int
    {
        $query = QueryBuilder::for(Developer::class)
            ->where('recommended_by_us', true)
            ->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::callback('job_title.name', function ($query, $value) {
                    $values = $this->parseFilterValues($value);
                    if (empty($values)) {
                        return;
                    }
                    $query->whereHas('jobTitle', function ($q) use ($values) {
                        $q->where(function ($q) use ($values) {
                            foreach ($values as $val) {
                                $term = '%'.addcslashes($val, '%_').'%';
                                $q->orWhere('name', 'like', $term);
                            }
                        });
                    });
                }),
                AllowedFilter::callback('search', function ($query, $value) {
                    if (blank($value)) {
                        return;
                    }
                    $term = '%'.addcslashes($value, '%_').'%';
                    $query->where(function ($query) use ($term) {
                        $query->where('developers.name', 'like', $term)
                            ->orWhere('developers.email', 'like', $term)
                            ->orWhereHas('skills', function ($q) use ($term) {
                                $q->where('skills.name', 'like', $term);
                            });
                    });
                }),
                AllowedFilter::callback('skill', function ($query, $value) {
                    $values = $this->parseFilterValues($value);
                    if (empty($values)) {
                        return;
                    }
                    $query->whereHas('skills', function ($q) use ($values) {
                        $q->where(function ($q) use ($values) {
                            foreach ($values as $val) {
                                $term = '%'.addcslashes($val, '%_').'%';
                                $q->orWhere('skills.name', 'like', $term);
                            }
                        });
                    });
                }),
                AllowedFilter::callback('years_min', function ($query, $value) {
                    if ($value === null || $value === '') {
                        return;
                    }
                    $query->where('years_of_experience', '>=', (int) $value);
                }),
                AllowedFilter::callback('years_max', function ($query, $value) {
                    if ($value === null || $value === '') {
                        return;
                    }
                    $query->where('years_of_experience', '<=', (int) $value);
                }),
            ]);

        return $query->count();
    }

    /**
     * Parse filter value (handles comma-separated for multi-select).
     *
     * @return array<string>
     */
    private function parseFilterValues(mixed $value): array
    {
        if (blank($value)) {
            return [];
        }
        $values = is_array($value) ? $value : explode(',', (string) $value);

        return array_values(array_filter(array_map('trim', $values)));
    }
}
