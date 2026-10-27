<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffProfile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'phone', 'job_title', 'bio', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workingHours(): HasMany
    {
        return $this->hasMany(StaffWorkingHour::class, 'staff_profile_id');
    }

    public function blackoutDates(): HasMany
    {
        return $this->hasMany(StaffBlackoutDate::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_staff');
    }

    /**
     * Map locations through the user_id matching the staff_id on the pivot
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(
            Location::class,
            'location_staff',
            'staff_id',       // Foreign key on the pivot table representing the staff (User ID)
            'location_id',    // Foreign key on the pivot table representing the location
            'user_id',        // Local key on the StaffProfile model to match against staff_id
            'id'              // Local key on the Location model
        );
    }
}
