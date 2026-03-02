<?php

namespace App\Models;

use App\Helpers\StorageHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Hackathon extends Model
{
    /** @use HasFactory<\Database\Factories\HackathonFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'body',
        'image',
        'youtube_url',
        'reward_badge_id',
        'reward_description',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function rewardBadge(): BelongsTo
    {
        return $this->belongsTo(Badge::class, 'reward_badge_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Hackathon $hackathon): void {
            if (empty($hackathon->slug)) {
                $hackathon->slug = Str::slug($hackathon->title);
            }
        });

        static::updating(function (Hackathon $hackathon): void {
            if ($hackathon->isDirty('title') && empty($hackathon->slug)) {
                $hackathon->slug = Str::slug($hackathon->title);
            }
        });
    }

    public function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => StorageHelper::url($this->image),
        );
    }
}
