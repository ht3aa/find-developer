<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HackathonAttendance extends Model
{
    protected $fillable = [
        'developer_id',
        'hackathon_id',
        'date',
        'attended',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'attended' => 'boolean',
        ];
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    public function hackathon(): BelongsTo
    {
        return $this->belongsTo(Hackathon::class);
    }
}
