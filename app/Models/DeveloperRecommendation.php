<?php

namespace App\Models;

use App\Enums\RecommendationStatus;
use App\Observers\DeveloperRecommendationObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(DeveloperRecommendationObserver::class)]
class DeveloperRecommendation extends Model
{
    protected $fillable = [
        'recommender_id',
        'recommended_id',
        'recommendation_note',
        'status',
    ];

    protected $casts = [
        'status' => RecommendationStatus::class,
    ];

    public function recommender(): BelongsTo
    {
        return $this->belongsTo(Developer::class, 'recommender_id');
    }

    public function recommended(): BelongsTo
    {
        return $this->belongsTo(Developer::class, 'recommended_id');
    }
}
