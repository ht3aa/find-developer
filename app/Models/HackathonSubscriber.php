<?php

namespace App\Models;

use App\Enums\HackathonSubscriberStatus;
use App\Observers\HackathonSubscriberObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([HackathonSubscriberObserver::class])]
class HackathonSubscriber extends Model
{
    protected $fillable = [
        'hackathon_id',
        'developer_id',
        'message',
        'status',
        'is_attended',
    ];

    protected function casts(): array
    {
        return [
            'status' => HackathonSubscriberStatus::class,
            'is_attended' => 'boolean',
        ];
    }

    public function hackathon(): BelongsTo
    {
        return $this->belongsTo(Hackathon::class);
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }
}
