<?php

namespace App\Listeners\Backend;

use App\Events\Backend\RoleModified;
use Illuminate\Support\Facades\Log;

class LogRoleActivity
{
    public function handle(RoleModified $event): void
    {
        // Load permissions if they exist (will be empty on delete if cascaded, but useful for create/update)
        $permissions = $event->role->exists ? $event->role->permissions->pluck('name')->toArray() : [];

        // Write a detailed security audit trail so the owner can answer "Who changed this permission?"[cite: 7]
        Log::info("Role {$event->action}", [
            'actor_id' => $event->actor->id,
            'actor_email' => $event->actor->email,
            'role_id' => $event->role->id,
            'role_name' => $event->role->name,
            'role_slug' => $event->role->slug,
            'attached_permissions' => $permissions,
        ]);
    }
}
