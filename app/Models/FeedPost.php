<?php

namespace App\Models;

use Database\Factories\FeedPostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedPost extends Model
{
    /** @use HasFactory<FeedPostFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'body',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(FeedPostImage::class)->orderBy('sort_order');
    }

    public function likers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'feed_post_likes')->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(FeedPostComment::class);
    }

    public function rootComments(): HasMany
    {
        return $this->hasMany(FeedPostComment::class)->whereNull('parent_id')->orderBy('created_at');
    }
}
