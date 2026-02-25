<?php

namespace App\Models;

use App\Enums\BlogStatus;
use App\Helpers\StorageHelper;
use App\Models\Scopes\DeveloperScope;
use App\Observers\DeveloperBlogObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[ScopedBy([DeveloperScope::class])]
#[ObservedBy(DeveloperBlogObserver::class)]
class DeveloperBlog extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'developer_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'published_at',
    ];

    protected $casts = [
        'status' => BlogStatus::class,
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);
            }
        });

        static::updating(function ($blog) {
            if ($blog->isDirty('title') && empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);
            }
        });
    }

    public function featureImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => StorageHelper::url($this->featured_image),
        );
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'developer_blog_id');
    }

    public function rootComments(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'developer_blog_id')->whereNull('parent_id');
    }

    public function likers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'blog_likes')->withTimestamps();
    }

    public function scopePublished($query)
    {
        return $query->where('status', BlogStatus::PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly(['*']);
    }
}
