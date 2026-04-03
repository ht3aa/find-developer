<?php

namespace App\Models;

use App\Enums\Currency;
use App\Enums\JobStatus;
use App\Enums\WorldGovernorate;
use Database\Factories\CompanyJobFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class CompanyJob extends Model
{
    /** @use HasFactory<CompanyJobFactory> */
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
        'user_id',
        'first_payment_qi_confirmed',
        'gitea_owner',
        'gitea_repo_name',
        'gitea_provisioned_at',
    ];

    protected $casts = [
        'status' => JobStatus::class,
        'salary_currency' => Currency::class,
        'location' => WorldGovernorate::class,
        'first_payment_qi_confirmed' => 'boolean',
        'gitea_provisioned_at' => 'datetime',
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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<CompanyJobApplication, $this>
     */
    public function applications(): HasMany
    {
        return $this->hasMany(CompanyJobApplication::class);
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
            ->dontLogEmptyChanges()
            ->logOnly(['*']);
    }
}
