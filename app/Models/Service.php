<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'slug', 'short_description', 'description',
        'price', 'duration_minutes', 'image', 'is_active', 'is_featured',
        'sort_order', 'meta_title', 'meta_description', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The staff (users) assigned to perform this service.
     */
    public function staff(): BelongsToMany
    {
        // Explicitly defining the related model (User), the pivot table (staff_services),
        // the foreign key for this model (service_id), and the foreign key for the related model (user_id).
        return $this->belongsToMany(User::class, 'staff_services', 'service_id', 'user_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * The FAQ entries that are shown on this service's page.
     */
    public function faqs(): BelongsToMany
    {
        return $this->belongsToMany(Faq::class, 'service_faqs', 'service_id', 'faq_id');
    }

    /**
     * The public URL of the service image (stored on the public disk).
     */
    public function imageUrl(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://') || str_starts_with($this->image, '//')) {
            return $this->image;
        }

        return Storage::disk('public')->url($this->image);
    }
}
