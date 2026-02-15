<?php

namespace App\Models;

use App\Models\Scopes\DeveloperScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\JobTitle;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[ScopedBy([DeveloperScope::class])]
class DeveloperCompany extends Model
{
    /** @use HasFactory<\Database\Factories\DeveloperCompanyFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = [
        'developer_id',
        'company_name',
        'job_title_id',
        'description',
        'start_date',
        'end_date',
        'is_current',
        'parent_id',
        'show_company',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'show_company' => 'boolean',
    ];

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DeveloperCompany::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(DeveloperCompany::class, 'parent_id')->orderByDesc('start_date');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeVisible($query)
    {
        return $query->where('show_company', true);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly(['*']);
    }
}
