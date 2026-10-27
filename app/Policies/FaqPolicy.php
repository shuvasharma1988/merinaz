<?php

namespace App\Policies;

use App\Models\Faq;
use App\Models\User;

class FaqPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('faqs.view');
    }

    public function view(User $user, Faq $faq): bool
    {
        return $user->hasPermission('faqs.view.detail') || $user->hasPermission('faqs.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('faqs.create');
    }

    public function update(User $user, Faq $faq): bool
    {
        return $user->hasPermission('faqs.edit');
    }

    public function delete(User $user, Faq $faq): bool
    {
        return $user->hasPermission('faqs.delete');
    }
}
