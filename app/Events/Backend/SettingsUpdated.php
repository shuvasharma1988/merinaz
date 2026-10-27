<?php

namespace App\Events\Backend;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SettingsUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $actor,
        public array $updatedKeys
    ) {}
}
