<?php

namespace App\Models;

use App\Casts\AvailabilityTypeArray;
use App\Enums\Currency;
use App\Enums\DeveloperStatus;
use App\Enums\SubscriptionPlan;
use App\Enums\WorldGovernorate;
use App\Helpers\StorageHelper;
use App\Models\Scopes\ApprovedScope;
use App\Observers\AdminDeveloperObserver;
use App\Observers\DeveloperObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[ScopedBy([ApprovedScope::class])]
#[ObservedBy([DeveloperObserver::class, AdminDeveloperObserver::class])]
class Developer extends Model
{
    use HasFactory, LogsActivity, Notifiable;

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
        'youtube_url',
        'cv_path',
        'update_cv_automatic',
        'location',
        'expected_salary_from',
        'expected_salary_to',
        'salary_currency',
        'is_available',
        'special_needs',
        'availability_type',
        'recommended_by_us',
        'status',
        'subscription_plan',
    ];

    protected $casts = [
        'years_of_experience' => 'integer',
        'expected_salary_from' => 'integer',
        'expected_salary_to' => 'integer',
        'is_available' => 'boolean',
        'update_cv_automatic' => 'boolean',
        'special_needs' => 'boolean',
        'recommended_by_us' => 'boolean',
        'status' => DeveloperStatus::class,
        'subscription_plan' => SubscriptionPlan::class,
        'location' => WorldGovernorate::class,
        'salary_currency' => Currency::class,
        'availability_type' => AvailabilityTypeArray::class,
    ];

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

    public function companies(): HasMany
    {
        return $this->hasMany(DeveloperCompany::class);
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

    public function recommendationsGiven(): HasMany
    {
        return $this->hasMany(DeveloperRecommendation::class, 'recommender_id');
    }

    public function recommendationsReceived(): HasMany
    {
        return $this->hasMany(DeveloperRecommendation::class, 'recommended_id');
    }

    /**
     * Get offers where this developer is in developer_ids.
     *
     * @return Builder<DeveloperOffer>
     */
    public function offers(): Builder
    {
        return DeveloperOffer::query()->whereJsonContains('developer_ids', $this->id);
    }

    public function blogs(): HasMany
    {
        return $this->hasMany(DeveloperBlog::class);
    }

    public function hackathonSubscriptions(): HasMany
    {
        return $this->hasMany(HackathonSubscriber::class, 'developer_id');
    }

    public function profileViews(): HasMany
    {
        return $this->hasMany(DeveloperProfileView::class);
    }

    public function experienceTasks(): BelongsToMany
    {
        return $this->belongsToMany(ExperienceTask::class, 'experience_task_developer')
            ->withTimestamps();
    }

    public function cvPathUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => StorageHelper::url($this->cv_path),
        );
    }

    public function youtubeVideoId(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->youtube_url) {
                    return null;
                }

                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/', $this->youtube_url, $matches)) {
                    return $matches[1];
                }

                return null;
            },
        );
    }

    public function recommendedDevelopers(): BelongsToMany
    {
        return $this->belongsToMany(
            Developer::class,
            'developer_recommendations',
            'recommender_id',
            'recommended_id'
        )->withTimestamps();
    }

    public function recommendedByDevelopers(): BelongsToMany
    {
        return $this->belongsToMany(
            Developer::class,
            'developer_recommendations',
            'recommended_id',
            'recommender_id'
        )->withTimestamps();
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

    /**
     * Scope: developers eligible for the newsletter (available, 2+ badges,
     * work experience, projects, CV, and skills).
     */
    public function scopeEligibleForNewsletter($query)
    {
        return $query
            ->available()
            ->withCount('badges')
            ->having('badges_count', '>=', 2)
            ->whereHas('companies')
            ->whereHas('projects')
            ->whereHas('skills')
            ->whereNotNull('cv_path')
            ->where('cv_path', '!=', '');
    }

    /**
     * Whether this developer meets all newsletter eligibility requirements.
     * Uses withCount attributes when present to avoid N+1.
     */
    public function meetsNewsletterRequirements(): bool
    {
        if (! $this->is_available) {
            return false;
        }

        $badgesCount = array_key_exists('badges_count', $this->attributes)
            ? (int) $this->attributes['badges_count']
            : $this->badges()->count();
        if ($badgesCount < 2) {
            return false;
        }

        $hasCompanies = array_key_exists('companies_count', $this->attributes)
            ? (int) $this->attributes['companies_count'] > 0
            : $this->companies()->exists();
        if (! $hasCompanies) {
            return false;
        }

        $hasProjects = array_key_exists('projects_count', $this->attributes)
            ? (int) $this->attributes['projects_count'] > 0
            : $this->projects()->exists();
        if (! $hasProjects) {
            return false;
        }

        $hasSkills = array_key_exists('skills_count', $this->attributes)
            ? (int) $this->attributes['skills_count'] > 0
            : $this->skills()->exists();
        if (! $hasSkills) {
            return false;
        }

        if (empty(trim((string) ($this->cv_path ?? '')))) {
            return false;
        }

        return true;
    }

    public function getCurrencyAttribute(): string
    {
        return $this->salary_currency?->value ?? Currency::IQD->value;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->logOnly(['*']);
    }
}
