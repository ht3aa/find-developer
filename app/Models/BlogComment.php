<?php

namespace App\Models;

use App\Enums\BlogCommentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogComment extends Model
{
    protected $fillable = [
        'developer_blog_id',
        'parent_id',
        'user_id',
        'name',
        'email',
        'body',
        'status',
    ];

    protected $casts = [
        'status' => BlogCommentStatus::class,
    ];

    public function developerBlog(): BelongsTo
    {
        return $this->belongsTo(DeveloperBlog::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(BlogComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'parent_id')->orderBy('created_at');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'blog_comment_likes')->withTimestamps();
    }

    public function scopeApproved($query)
    {
        return $query->where('status', BlogCommentStatus::APPROVED);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
