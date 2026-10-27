<?php

namespace App\Listeners\Backend;

use App\Events\Backend\ServiceModified;
use Illuminate\Support\Facades\Log;

class LogServiceActivity
{
    public function handle(ServiceModified $event): void
    {
        Log::info("Service {$event->action}: {$event->service->name} (ID: {$event->service->id}) by User ID: ".auth()->id());
    }
}
