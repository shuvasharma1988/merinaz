<?php

namespace App\Events\Backend;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StaffPermissionsUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $actor,
        public User $targetUser
    ) {}
}
