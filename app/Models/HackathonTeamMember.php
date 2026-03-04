<?php

namespace App\Models;

use App\Enums\HackathonMemberPosition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HackathonTeamMember extends Model
{
    protected $fillable = [
        'hackathon_team_id',
        'developer_id',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'position' => HackathonMemberPosition::class,
        ];
    }

    public function hackathonTeam(): BelongsTo
    {
        return $this->belongsTo(HackathonTeam::class);
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }
}
