<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffBlackoutDate extends Model
{
    protected $fillable = [
        'staff_profile_id', 'date', 'reason',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(StaffProfile::class, 'staff_profile_id');
    }
}
