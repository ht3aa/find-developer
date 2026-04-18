<?php

namespace App\Models;

use Database\Factories\FeedPostCommentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedPostComment extends Model
{
    /** @use HasFactory<FeedPostCommentFactory> */
    use HasFactory;

    protected $fillable = [
        'feed_post_id',
        'parent_id',
        'user_id',
        'body',
    ];

    public function feedPost(): BelongsTo
    {
        return $this->belongsTo(FeedPost::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(FeedPostComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(FeedPostComment::class, 'parent_id')->orderBy('created_at');
    }
}
