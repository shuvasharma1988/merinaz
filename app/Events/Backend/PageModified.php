<?php

namespace App\Events\Backend;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PageModified
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $actor,
        public Page $page,
        public string $action // 'created', 'updated', 'deleted'
    ) {}
}
