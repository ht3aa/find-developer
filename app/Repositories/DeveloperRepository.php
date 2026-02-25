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
            ->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::partial('job_title.name'),
                AllowedFilter::callback('search', function ($query, $value) {
                    if (blank($value)) {
                        return;
                    }
                    $term = '%'.addcslashes($value, '%_').'%';
                    $query->where(function ($query) use ($term) {
                        $query->where('developers.name', 'like', $term)
                            ->orWhereHas('jobTitle', function ($q) use ($term) {
                                $q->where('name', 'like', $term);
                            })
                            ->orWhereHas('skills', function ($q) use ($term) {
                                $q->where('skills.name', 'like', $term);
                            });
                    });
                }),
                AllowedFilter::callback('skill', function ($query, $value) {
                    if (blank($value)) {
                        return;
                    }
                    $term = '%'.addcslashes($value, '%_').'%';
                    $query->whereHas('skills', function ($q) use ($term) {
                        $q->where('skills.name', 'like', $term);
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
            ->defaultSort('name')
            ->allowedSorts(['name', 'years_of_experience', 'created_at'])
            ->paginate($perPage)
            ->withQueryString();
    }
}
