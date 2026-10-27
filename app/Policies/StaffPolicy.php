<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StaffPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('staff.view');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermission('staff.view');
    }

    public function viewDetail(User $user, User $model): bool
    {
        return $user->hasPermission('staff.view.detail');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('staff.create');
    }

    public function update(User $user, User $model): bool
    {
        // Security constraint: A standard manager cannot edit the Owner's profile
        if ($model->hasRole('owner') && ! $user->hasRole('owner')) {
            return false;
        }

        return $user->hasPermission('staff.edit');
    }

    public function delete(User $user, User $model): bool
    {
        // Absolute protection: No one can delete an owner
        if ($model->hasRole('owner')) {
            return false;
        }

        // Prevent users from deleting themselves
        if ($user->id === $model->id) {
            return false;
        }

        return $user->hasPermission('staff.delete');
    }
}
