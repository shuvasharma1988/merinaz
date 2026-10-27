<?php

namespace App\Listeners\Auth;

use App\Events\Auth\PasswordReset;
use Illuminate\Support\Facades\Log;

class LogPasswordReset
{
    /**
     * Handle the event to maintain a security audit trail.
     */
    public function handle(PasswordReset $event): void
    {
        Log::info('User password reset successfully', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'role_type' => $event->user->isAdmin() ? 'Admin/Staff' : 'Customer',
        ]);
    }
}
