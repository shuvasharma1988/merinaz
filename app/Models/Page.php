<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    public const PROTECTED_SLUGS = [
        'about',
        'about-us',
        'privacy-policy',
        'terms',
        'terms-of-service',
        'cookie-policy',
    ];

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'is_published',
        'is_system',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_system' => 'boolean',
    ];

    /**
     * Checks if the page has a protected core slug or system flag.
     */
    /**
     * Determine if this page is a protected core page.
     */
    public function isProtectedSlug(): bool
    {
        if ((bool) $this->is_system) {
            return true;
        }

        return in_array((string) $this->slug, self::PROTECTED_SLUGS, true);
    }

    /**
     * Alias $page->body to the `content` column for your Blade templates.
     */
    public function getBodyAttribute(): ?string
    {
        return $this->content;
    }

    public function setBodyAttribute(?string $value): void
    {
        $this->attributes['content'] = $value;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
