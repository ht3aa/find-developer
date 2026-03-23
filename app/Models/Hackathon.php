<?php

namespace App\Models;

use App\Helpers\StorageHelper;
use Database\Factories\HackathonFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Hackathon extends Model
{
    /** @use HasFactory<HackathonFactory> */
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
        'current_team_id_to_vote',
        'enable_voting',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'enable_voting' => 'boolean',
        ];
    }

    public function rewardBadge(): BelongsTo
    {
        return $this->belongsTo(Badge::class, 'reward_badge_id');
    }

    public function subscribers(): HasMany
    {
        return $this->hasMany(HackathonSubscriber::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(HackathonAttendance::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(HackathonTeam::class);
    }

    public function currentVotingTeam(): BelongsTo
    {
        return $this->belongsTo(HackathonTeam::class, 'current_team_id_to_vote');
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
            get: fn () => StorageHelper::url($this->image),
        );
    }

    public function youtubeVideoId(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->youtube_url) {
                    return null;
                }

                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $this->youtube_url, $matches)) {
                    return $matches[1];
                }

                return null;
            },
        );
    }
}
