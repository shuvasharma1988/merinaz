<?php

namespace App\Listeners\Backend;

use App\Events\Backend\StaffPermissionsUpdated;
use Illuminate\Support\Facades\Log;

class LogPermissionChange
{
    public function handle(StaffPermissionsUpdated $event): void
    {
        Log::info('Staff roles or permissions were modified', [
            'actor_id' => $event->actor->id,
            'actor_email' => $event->actor->email,
            'target_user_id' => $event->targetUser->id,
            'target_user_email' => $event->targetUser->email,
            'new_roles' => $event->targetUser->roles->pluck('name')->toArray(),
            'new_permissions' => $event->targetUser->permissions->pluck('name')->toArray(),
        ]);
    }
}
