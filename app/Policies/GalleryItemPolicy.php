<?php

namespace App\Policies;

use App\Models\GalleryItem;
use App\Models\User;

class GalleryItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('gallery.view');
    }

    public function view(User $user, GalleryItem $galleryItem): bool
    {
        return $user->hasPermission('gallery.view.detail') || $user->hasPermission('gallery.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('gallery.create');
    }

    public function update(User $user, GalleryItem $galleryItem): bool
    {
        return $user->hasPermission('gallery.edit');
    }

    public function delete(User $user, GalleryItem $galleryItem): bool
    {
        return $user->hasPermission('gallery.delete');
    }
}
