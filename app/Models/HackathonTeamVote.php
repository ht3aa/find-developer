<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HackathonTeamVote extends Model
{
    protected $fillable = [
        'hackathon_team_id',
        'subscriber_developer_id',
        'is_voted',
    ];

    protected function casts(): array
    {
        return [
            'is_voted' => 'boolean',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(HackathonTeam::class, 'hackathon_team_id');
    }

    public function subscriberDeveloper(): BelongsTo
    {
        return $this->belongsTo(Developer::class, 'subscriber_developer_id');
    }
}
