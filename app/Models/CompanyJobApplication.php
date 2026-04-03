<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Database\Factories\CompanyJobApplicationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class CompanyJobApplication extends Model
{
    /** @use HasFactory<CompanyJobApplicationFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = [
        'company_job_id',
        'developer_id',
        'status',
        'cover_message',
    ];

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
        ];
    }

    public function companyJob(): BelongsTo
    {
        return $this->belongsTo(CompanyJob::class);
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->logOnly(['*']);
    }
}
