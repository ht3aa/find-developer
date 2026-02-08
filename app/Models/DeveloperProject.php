<?php

namespace App\Models;

use App\Models\Scopes\DeveloperScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[ScopedBy([DeveloperScope::class])]
class DeveloperProject extends Model
{
    /** @use HasFactory<\Database\Factories\DeveloperProjectFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = [
        'developer_id',
        'title',
        'description',
        'link',
        'show_project',
    ];

    protected $casts = [
        'show_project' => 'boolean',
    ];

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('show_project', true);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly(['*']);
    }
}
