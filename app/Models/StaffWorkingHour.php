<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffWorkingHour extends Model
{
    protected $table = 'staff_working_hours';

    protected $fillable = [
        'staff_profile_id', 'day_of_week', 'start_time', 'end_time',
        'break_start_time', 'break_end_time', 'is_day_off',
    ];

    protected $casts = [
        'is_day_off' => 'boolean',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(StaffProfile::class, 'staff_profile_id');
    }
}
