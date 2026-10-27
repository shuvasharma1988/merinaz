<?php

namespace App\Events\Backend;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoleModified
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $actor,
        public string $action, // 'created', 'updated', 'deleted'
        public Role $role
    ) {}
}
