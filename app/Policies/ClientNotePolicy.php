<?php

namespace App\Policies;

use App\Models\ClientNote;
use App\Models\User;

class ClientNotePolicy
{
    public function view(User $user, ClientNote $note): bool
    {
        if ($user->hasAnyRole(['owner', 'manager'])) {
            return true;
        }

        if ($note->visibility === 'team') {
            return $user->hasRole('staff');
        }

        if ($note->visibility === 'private') {
            return $user->id === $note->staff_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['owner', 'manager', 'staff']);
    }

    public function update(User $user, ClientNote $note): bool
    {
        return $user->hasAnyRole(['owner', 'manager']) || $user->id === $note->staff_id;
    }

    public function delete(User $user, ClientNote $note): bool
    {
        return $user->hasAnyRole(['owner', 'manager']) || $user->id === $note->staff_id;
    }
}
