<?php

namespace App\Policies;

use App\Models\ContactMessage;
use App\Models\User;

class ContactMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('messages.view');
    }

    public function view(User $user, ContactMessage $message): bool
    {
        return $user->hasPermission('messages.view.detail') || $user->hasPermission('messages.view');
    }

    public function reply(User $user, ContactMessage $message): bool
    {
        return $user->hasPermission('messages.reply');
    }

    public function delete(User $user, ContactMessage $message): bool
    {
        return $user->hasPermission('messages.delete');
    }
}
