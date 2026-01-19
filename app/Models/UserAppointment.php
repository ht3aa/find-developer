<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\UserScope;

#[ScopedBy([UserScope::class])]
class UserAppointment extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'developer_id',
        'user_service_id',
        'status',
        'start_datetime',
        'notes',
    ];

    protected $casts = [
        'status' => AppointmentStatus::class,
        'start_datetime' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(UserService::class, 'user_service_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly(['*']);
    }
}
