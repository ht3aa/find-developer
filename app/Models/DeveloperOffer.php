<?php

namespace App\Models;

use App\Enums\AvailabilityType;
use App\Enums\OfferStatus;
use App\Observers\DeveloperOfferObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(DeveloperOfferObserver::class)]
class DeveloperOffer extends Model
{
    protected $fillable = [
        'developer_id',
        'user_id',
        'company_name',
        'job_title_id',
        'message',
        'salary_range',
        'work_type',
        'contact_email',
        'status',
    ];

    protected $casts = [
        'status' => OfferStatus::class,
        'work_type' => AvailabilityType::class,
    ];

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class, 'developer_id');
    }

    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class, 'job_title_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
