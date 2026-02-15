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
