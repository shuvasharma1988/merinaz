<?php

namespace App\Policies;

use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('services.view');
    }

    public function view(User $user): bool
    {
        return $user->hasPermission('services.view');
    }

    public function viewDetail(User $user): bool
    {
        return $user->hasPermission('services.view.detail');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('services.create');
    }

    public function update(User $user): bool
    {
        return $user->hasPermission('services.edit');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermission('services.delete');
    }
}
