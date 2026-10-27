<?php

namespace App\Listeners\Auth;

use App\Events\Auth\UserLoggedIn;
use Illuminate\Support\Facades\Log;

class LogSuccessfulLogin
{
    public function handle(UserLoggedIn $event): void
    {
        Log::info('User logged in', [
            'user_id' => $event->user->id,
            'ip_address' => $event->ipAddress,
            'user_agent' => $event->userAgent,
            'role_type' => $event->user->isAdmin() ? 'Admin/Staff' : 'Customer',
        ]);
    }
}
