<?php

namespace App\Events\Backend;

use App\Models\Service;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceModified
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Service $service, public string $action) {}
}
