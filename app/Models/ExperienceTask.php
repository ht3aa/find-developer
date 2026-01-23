<?php

namespace App\Models;

use App\Enums\Currency;
use App\Enums\ExperienceGain;
use App\Enums\ExperienceTaskStatus;
use App\Models\Scopes\ApprovedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ExperienceTask extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'required_developers_count',
        'status',
        'price',
        'price_currency',
        'created_by',
        'experience_gain',
    ];

    protected $casts = [
        'required_developers_count' => 'integer',
        'experience_gain' => ExperienceGain::class,
        'status' => ExperienceTaskStatus::class,
        'price_currency' => Currency::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            if (empty($task->slug)) {
                $task->slug = Str::slug($task->title);
            }
        });

        static::updating(function ($task) {
            if ($task->isDirty('title') && empty($task->slug)) {
                $task->slug = Str::slug($task->title);
            }
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function developers(): BelongsToMany
    {
        return $this->belongsToMany(Developer::class, 'experience_task_developer')
            ->withTimestamps();
    }

    public function scopeOpen($query)
    {
        return $query->where('status', ExperienceTaskStatus::OPEN);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', ExperienceTaskStatus::IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', ExperienceTaskStatus::COMPLETED);
    }

    public function scopeAvailable($query)
    {
        return $query->whereIn('status', [
            ExperienceTaskStatus::OPEN,
            ExperienceTaskStatus::IN_PROGRESS,
        ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly(['*']);
    }
}
