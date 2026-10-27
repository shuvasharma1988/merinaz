<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('roles.manage');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasPermission('roles.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('roles.manage');
    }

    public function update(User $user, Role $role): bool
    {
        // Absolute protection: No one (not even an owner) can modify the core owner role's baseline parameters
        if ($role->slug === 'owner') {
            return false;
        }

        return $user->hasPermission('roles.manage');
    }

    public function delete(User $user, Role $role): bool
    {
        // Absolute protection: Prevent deletion of core system roles
        if (in_array($role->slug, ['owner', 'manager', 'staff'])) {
            return false;
        }

        return $user->hasPermission('roles.manage');
    }
}
