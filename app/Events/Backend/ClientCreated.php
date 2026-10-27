<?php

namespace App\Events\Backend;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClientCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $client;

    public function __construct(User $client)
    {
        $this->client = $client;
    }
}
