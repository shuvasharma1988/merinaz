<?php

namespace App\Models;

use App\Enums\GalleryMediaType;
use App\Models\Concerns\HasStoredMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GalleryItem extends Model
{
    use HasFactory, HasStoredMedia, SoftDeletes;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'media_type',
        'image',
        'video',
        'thumbnail',
        'alt_text',
        'caption',
        'description',
        'duration_seconds',
        'sort_order',
        'is_published',
        'meta_title',
        'meta_description',
        'canonical_url',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'media_type' => GalleryMediaType::class,
        'is_published' => 'boolean',
        'sort_order' => 'integer',
        'duration_seconds' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(GalleryCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('created_at');
    }

    public function isImage(): bool
    {
        return $this->media_type === GalleryMediaType::Image;
    }

    public function isVideo(): bool
    {
        return $this->media_type === GalleryMediaType::Video;
    }

    public function imageUrl(): ?string
    {
        return $this->mediaUrl($this->image);
    }

    public function videoUrl(): ?string
    {
        return $this->mediaUrl($this->video);
    }

    public function thumbnailUrl(): ?string
    {
        return $this->mediaUrl($this->thumbnail) ?? $this->imageUrl();
    }
}
