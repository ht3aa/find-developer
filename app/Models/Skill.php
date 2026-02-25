<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Skill extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($skill) {
            if (empty($skill->slug)) {
                $skill->slug = Str::slug($skill->name);
            }
        });
    }

    #[Scope]
    protected function active($query)
    {
        return $query->where('is_active', true);
    }

    public function developers(): BelongsToMany
    {
        return $this->belongsToMany(Developer::class, 'developer_skill')
            ->withTimestamps();
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_skill')
            ->withTimestamps();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly(['*']);
    }
}
