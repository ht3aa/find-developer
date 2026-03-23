<?php

namespace App\Models;

use App\Enums\AvailabilityType;
use App\Enums\OfferStatus;
use App\Observers\DeveloperOfferObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(DeveloperOfferObserver::class)]
class DeveloperOffer extends Model
{
    protected $fillable = [
        'developer_ids',
        'user_id',
        'company_name',
        'job_title_id',
        'message',
        'salary_range',
        'work_type',
        'contact_email',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'developer_ids' => 'array',
            'status' => OfferStatus::class,
            'work_type' => AvailabilityType::class,
        ];
    }

    /**
     * Get developers for this offer via developer_ids.
     *
     * @return Collection<int, Developer>
     */
    public function developers(): Collection
    {
        $ids = $this->developer_ids ?? [];
        if (empty($ids)) {
            return new Collection;
        }

        return Developer::with('jobTitle')->whereIn('id', $ids)->get();
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
