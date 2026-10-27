<?php

namespace App\Events\Backend;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContactMessageReplied
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $actor,
        public ContactMessage $contactMessage
    ) {}
}
