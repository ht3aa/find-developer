<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class JobTitle extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($jobTitle) {
            if (empty($jobTitle->slug)) {
                $jobTitle->slug = Str::slug($jobTitle->name);
            }
        });

        static::updating(function ($jobTitle) {
            if ($jobTitle->isDirty('name')) {
                $jobTitle->slug = Str::slug($jobTitle->name);
            }
        });
    }

    #[Scope]
    protected function active($query)
    {
        return $query->where('is_active', true);
    }

    public function developers(): HasMany
    {
        return $this->hasMany(Developer::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly(['*']);
    }
}
