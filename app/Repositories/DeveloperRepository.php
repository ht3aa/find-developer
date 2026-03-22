<?php

namespace App\Repositories;

use App\Models\Developer;
use App\Models\JobTitle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
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
                'recommendationsReceived',
            ])
            ->withCount('recommendationsReceived')
            ->withCount('badges')
            ->allowedFilters(...[
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

                    if (is_array($value)) {
                        $value = implode(' ', $value);
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
                AllowedFilter::callback('preset_ids', function ($query, $value) {
                    $this->applyPresetIdsFilter($query, $value);
                }),
                AllowedFilter::callback('role_bands', function ($query, $value) {
                    $this->applyRoleBandsJsonFilter($query, $value);
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
                AllowedFilter::callback('availability_type', function ($query, $value) {
                    $values = $this->parseFilterValues($value);
                    if (empty($values)) {
                        return;
                    }
                    $query->where(function ($q) use ($values) {
                        foreach ($values as $val) {
                            $q->orWhereJsonContains('availability_type', $val);
                        }
                    });
                }),
                AllowedFilter::callback('has_urls', function ($query, $value) {
                    $values = $this->parseFilterValues($value);
                    if (empty($values)) {
                        return;
                    }
                    $columnMap = [
                        'github' => 'github_url',
                        'linkedin' => 'linkedin_url',
                        'portfolio' => 'portfolio_url',
                        'youtube' => 'youtube_url',
                    ];
                    $query->where(function ($q) use ($values, $columnMap) {
                        foreach ($values as $val) {
                            $col = $columnMap[$val] ?? null;
                            if ($col) {
                                $q->orWhere(function ($q2) use ($col) {
                                    $q2->whereNotNull($col)->where($col, '!=', '');
                                });
                            }
                        }
                    });
                }),
                AllowedFilter::callback('badge', function ($query, $value) {
                    $values = $this->parseFilterValues($value);
                    if (empty($values)) {
                        return;
                    }
                    $query->whereHas('badges', function ($q) use ($values) {
                        $q->whereIn('badges.name', $values);
                    });
                }),
                AllowedFilter::callback('is_available', function ($query, $value) {
                    if ($value === null || $value === '') {
                        return;
                    }
                    $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    $query->where('developers.is_available', $bool);
                }),
                AllowedFilter::callback('is_recommended', function ($query, $value) {
                    if ($value === null || $value === '') {
                        return;
                    }
                    $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    $query->where('developers.recommended_by_us', $bool);
                }),
                AllowedFilter::callback('ids', function ($query, $value) {
                    $ids = $this->parseFilterValues($value);
                    if (empty($ids)) {
                        return;
                    }
                    $ids = array_map('intval', array_filter($ids, fn ($v) => is_numeric($v)));
                    if (empty($ids)) {
                        return;
                    }
                    $query->whereIn('developers.id', $ids);
                }),
            ])
            ->allowedSorts(...['name', 'years_of_experience', 'created_at'])
            ->orderBy('badges_count', 'desc')
            ->orderBy('recommendations_received_count', 'desc')
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
            ->allowedFilters(...[
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
                AllowedFilter::callback('preset_ids', function ($query, $value) {
                    $this->applyPresetIdsFilter($query, $value);
                }),
                AllowedFilter::callback('role_bands', function ($query, $value) {
                    $this->applyRoleBandsJsonFilter($query, $value);
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
                AllowedFilter::callback('availability_type', function ($query, $value) {
                    $values = $this->parseFilterValues($value);
                    if (empty($values)) {
                        return;
                    }
                    $query->where(function ($q) use ($values) {
                        foreach ($values as $val) {
                            $q->orWhereJsonContains('availability_type', $val);
                        }
                    });
                }),
                AllowedFilter::callback('has_urls', function ($query, $value) {
                    $values = $this->parseFilterValues($value);
                    if (empty($values)) {
                        return;
                    }
                    $columnMap = [
                        'github' => 'github_url',
                        'linkedin' => 'linkedin_url',
                        'portfolio' => 'portfolio_url',
                        'youtube' => 'youtube_url',
                    ];
                    $query->where(function ($q) use ($values, $columnMap) {
                        foreach ($values as $val) {
                            $col = $columnMap[$val] ?? null;
                            if ($col) {
                                $q->orWhere(function ($q2) use ($col) {
                                    $q2->whereNotNull($col)->where($col, '!=', '');
                                });
                            }
                        }
                    });
                }),
                AllowedFilter::callback('badge', function ($query, $value) {
                    $values = $this->parseFilterValues($value);
                    if (empty($values)) {
                        return;
                    }
                    $query->whereHas('badges', function ($q) use ($values) {
                        $q->whereIn('badges.name', $values);
                    });
                }),
                AllowedFilter::callback('is_available', function ($query, $value) {
                    if ($value === null || $value === '') {
                        return;
                    }
                    $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    $query->where('developers.is_available', $bool);
                }),
                AllowedFilter::callback('is_recommended', function ($query, $value) {
                    if ($value === null || $value === '') {
                        return;
                    }
                    $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    $query->where('developers.recommended_by_us', $bool);
                }),
            ]);

        return $query->count();
    }

    /**
     * OR together quick-role presets (job title + experience band per preset).
     *
     * @param  Builder<Developer>  $query
     */
    private function applyPresetIdsFilter(Builder $query, mixed $value): void
    {
        $ids = $this->parseFilterValues($value);
        $bands = [];
        foreach ($ids as $id) {
            $band = $this->rolePresetDefinition($id);
            if ($band !== null) {
                $bands[] = $band;
            }
        }
        if ($bands === []) {
            return;
        }

        $this->applyRoleBandsOr($query, $bands);
    }

    /**
     * Dynamic job title + experience bands from JSON (OR). Titles must match active job titles.
     *
     * @param  Builder<Developer>  $query
     */
    private function applyRoleBandsJsonFilter(Builder $query, mixed $value): void
    {
        if (! is_string($value) || $value === '') {
            return;
        }

        $decoded = base64_decode($value, true);
        if ($decoded === false || $decoded === '') {
            return;
        }

        if (strlen($decoded) > 4096) {
            return;
        }

        try {
            $parsed = json_decode($decoded, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return;
        }

        if (! is_array($parsed)) {
            return;
        }

        /** @var list<array{job_title: string, years_min: int|null, years_max: int|null}> $bands */
        $bands = [];
        foreach ($parsed as $item) {
            if (! is_array($item)) {
                continue;
            }
            $title = isset($item['job_title']) ? trim((string) $item['job_title']) : '';
            if ($title === '') {
                continue;
            }
            $bands[] = [
                'job_title' => $title,
                'years_min' => $this->filterYearBound($item['years_min'] ?? null),
                'years_max' => $this->filterYearBound($item['years_max'] ?? null),
            ];
        }

        $bands = array_slice($bands, 0, 20);
        if ($bands === []) {
            return;
        }

        $titles = array_values(array_unique(array_column($bands, 'job_title')));
        $validTitles = JobTitle::query()
            ->active()
            ->whereIn('name', $titles)
            ->pluck('name')
            ->all();
        $valid = array_flip($validTitles);

        $bands = array_values(array_filter($bands, fn (array $b) => isset($valid[$b['job_title']])));
        if ($bands === []) {
            return;
        }

        $this->applyRoleBandsOr($query, $bands);
    }

    /**
     * @param  list<array{job_title: string, years_min: int|null, years_max: int|null}>  $bands
     * @param  Builder<Developer>  $query
     */
    private function applyRoleBandsOr(Builder $query, array $bands): void
    {
        $query->where(function (Builder $outer) use ($bands) {
            foreach ($bands as $band) {
                $outer->orWhere(function (Builder $q) use ($band) {
                    $q->whereHas('jobTitle', function (Builder $jt) use ($band) {
                        $jt->where('name', $band['job_title']);
                    });
                    if ($band['years_min'] !== null) {
                        $q->where('years_of_experience', '>=', $band['years_min']);
                    }
                    if ($band['years_max'] !== null) {
                        $q->where('years_of_experience', '<=', $band['years_max']);
                    }
                });
            }
        });
    }

    private function filterYearBound(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return (int) $value;
        }

        return null;
    }

    /**
     * @return array{job_title: string, years_min: int|null, years_max: int|null}|null
     */
    private function rolePresetDefinition(string $id): ?array
    {
        return match ($id) {
            'fe-junior' => ['job_title' => 'Frontend Developer', 'years_min' => null, 'years_max' => 2],
            'fe-mid' => ['job_title' => 'Frontend Developer', 'years_min' => 3, 'years_max' => 5],
            'fe-senior' => ['job_title' => 'Frontend Developer', 'years_min' => 6, 'years_max' => null],
            'be-junior' => ['job_title' => 'Backend Developer', 'years_min' => null, 'years_max' => 2],
            'be-mid' => ['job_title' => 'Backend Developer', 'years_min' => 3, 'years_max' => 5],
            'be-senior' => ['job_title' => 'Backend Developer', 'years_min' => 6, 'years_max' => null],
            'fs-junior' => ['job_title' => 'Full Stack Developer', 'years_min' => null, 'years_max' => 2],
            'fs-mid' => ['job_title' => 'Full Stack Developer', 'years_min' => 3, 'years_max' => 5],
            'fs-senior' => ['job_title' => 'Full Stack Developer', 'years_min' => 6, 'years_max' => null],
            default => null,
        };
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
