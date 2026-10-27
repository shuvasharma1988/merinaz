<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'date_of_birth', 'gender', 'source',
        'status', 'tag', 'marketing_consent', 'sms_consent',
        'email_consent', 'has_medical_issue', 'phone',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'marketing_consent' => 'boolean',
        'sms_consent' => 'boolean',
        'email_consent' => 'boolean',
        'has_medical_issue' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Dynamic calculation for lifetime spend
    public function getLifetimeSpendAttribute(): float
    {
        // Assuming Appointment/Payment models exist.
        // Sums all completed appointment totals for this user.
        return $this->user->appointments()
            ->where('status', 'completed')
            ->sum('total') ?? 0.00;
    }

    // Dynamic calculation for total visits
    public function getTotalVisitsAttribute(): int
    {
        return $this->user->appointments()
            ->where('status', 'completed')
            ->count();
    }
}
