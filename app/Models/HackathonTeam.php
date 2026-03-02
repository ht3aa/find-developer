<?php

namespace App\Models;

use App\Helpers\StorageHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HackathonTeam extends Model
{
    protected $fillable = [
        'hackathon_id',
        'title',
        'logo',
    ];

    public function hackathon(): BelongsTo
    {
        return $this->belongsTo(Hackathon::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(HackathonTeamMember::class);
    }

    public function logoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => StorageHelper::url($this->logo),
        );
    }
}
