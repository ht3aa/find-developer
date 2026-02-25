<?php

namespace App\Models\Scopes;

use App\Enums\DeveloperStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ApprovedScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->whereIn('status', [DeveloperStatus::APPROVED, DeveloperStatus::EXPERIENCE_CHANGED]);
    }
}
