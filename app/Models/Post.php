<?php

namespace App\Models;

use App\Enums\PostStatus;
use App\Models\Concerns\HasStoredMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, HasStoredMedia, SoftDeletes;

    protected $fillable = [
        'author_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'featured_image_alt',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'twitter_card',
        'updated_by',
    ];

    protected $casts = [
        'status' => PostStatus::class,
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    /**
     * Restrict the query to posts that may be shown on the public site.
     *
     * A post is public when it is published (or scheduled for a moment that
     * has already passed) and its publication date has been reached.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->whereIn('status', [PostStatus::Published->value, PostStatus::Scheduled->value])
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('published_at')->orderByDesc('created_at');
    }

    public function isPubliclyVisible(): bool
    {
        if ($this->status === null || $this->published_at === null) {
            return false;
        }

        return $this->status->isPubliclyVisibleAt($this->published_at)
            && $this->published_at->lessThanOrEqualTo(now());
    }

    public function featuredImageUrl(): ?string
    {
        return $this->mediaUrl($this->featured_image);
    }

    public function ogImageUrl(): ?string
    {
        return $this->mediaUrl($this->og_image) ?? $this->featuredImageUrl();
    }
}
