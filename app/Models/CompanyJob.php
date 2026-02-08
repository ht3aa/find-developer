<?php

namespace App\Models;

use App\Enums\Currency;
use App\Enums\JobStatus;
use App\Enums\WorldGovernorate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CompanyJob extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'company_jobs';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'company_name',
        'email',
        'contact_link',
        'location',
        'job_title_id',
        'salary_from',
        'salary_to',
        'salary_currency',
        'requirements',
        'status',
    ];

    protected $casts = [
        'status' => JobStatus::class,
        'salary_currency' => Currency::class,
        'location' => WorldGovernorate::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($job) {
            if (empty($job->slug)) {
                $job->slug = Str::slug($job->title);
            }
        });

        static::updating(function ($job) {
            if ($job->isDirty('title') && empty($job->slug)) {
                $job->slug = Str::slug($job->title);
            }
        });
    }

    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', JobStatus::PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', JobStatus::APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', JobStatus::REJECTED);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly(['*']);
    }
}
