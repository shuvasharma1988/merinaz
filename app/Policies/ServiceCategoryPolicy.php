<?php

namespace App\Policies;

use App\Models\User;

class ServiceCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('service.category.view');
    }

    public function view(User $user): bool
    {
        return $user->hasPermission('service.category.view');
    }

    public function viewDetail(User $user): bool
    {
        return $user->hasPermission('service.category.view.detail');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('service.category.create');
    }

    public function update(User $user): bool
    {
        return $user->hasPermission('service.category.edit');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermission('service.category.delete');
    }
}
