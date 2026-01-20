<?php

namespace App\Models;

use App\Enums\DeveloperStatus;
use App\Enums\WorldGovernorate;
use App\Enums\Currency;
use App\Enums\SubscriptionPlan;
use App\Enums\AvailabilityType;
use App\Casts\AvailabilityTypeArray;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Scopes\ApprovedScope;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

#[ScopedBy([ApprovedScope::class])]
class Developer extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'user_id',
        'job_title_id',
        'years_of_experience',
        'bio',
        'portfolio_url',
        'github_url',
        'linkedin_url',
        'location',
        'expected_salary_from',
        'expected_salary_to',
        'salary_currency',
        'is_available',
        'availability_type',
        'recommended_by_us',
        'status',
        'subscription_plan',
    ];

    protected $hidden = [
        'phone',
    ];

    protected $casts = [
        'years_of_experience' => 'integer',
        'expected_salary_from' => 'integer',
        'expected_salary_to' => 'integer',
        'is_available' => 'boolean',
        'recommended_by_us' => 'boolean',
        'status' => DeveloperStatus::class,
        'subscription_plan' => SubscriptionPlan::class,
        'location' => WorldGovernorate::class,
        'salary_currency' => Currency::class,
        'availability_type' => AvailabilityTypeArray::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($developer) {
            if (empty($developer->slug)) {
                $developer->slug = Str::slug($developer->name);
            }
        });

        static::updating(function ($developer) {
            if ($developer->isDirty('name') && empty($developer->slug)) {
                $developer->slug = Str::slug($developer->name);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'developer_skill')
            ->withTimestamps();
    }

    public function projects(): HasMany
    {
        return $this->hasMany(DeveloperProject::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(UserAppointment::class);
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'developer_badge')
            ->withTimestamps();
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', DeveloperStatus::APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', DeveloperStatus::PENDING);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', DeveloperStatus::REJECTED);
    }

    public function scopeRecommended($query)
    {
        return $query->where('recommended_by_us', true);
    }

    public function isPremium(): bool
    {
        return $this->subscription_plan === SubscriptionPlan::PREMIUM;
    }

    public function scopeByExperience($query, $minYears, $maxYears = null)
    {
        $query->where('years_of_experience', '>=', $minYears);

        if ($maxYears !== null) {
            $query->where('years_of_experience', '<=', $maxYears);
        }

        return $query;
    }

    public function getCurrencyAttribute(): string
    {
        return $this->salary_currency?->value ?? Currency::IQD->value;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly(['*']);
    }
}
