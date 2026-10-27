<?php

namespace App\Policies;

use App\Models\ClientProfile;
use App\Models\User;

class ClientProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['owner', 'manager', 'staff']);
    }

    public function view(User $user, ClientProfile $clientProfile): bool
    {
        return $user->hasAnyRole(['owner', 'manager', 'staff']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['owner', 'manager', 'staff']);
    }

    public function update(User $user, ClientProfile $clientProfile): bool
    {
        return $user->hasAnyRole(['owner', 'manager', 'staff']);
    }

    public function delete(User $user, ClientProfile $clientProfile): bool
    {
        return $user->hasAnyRole(['owner', 'manager']);
    }
}
