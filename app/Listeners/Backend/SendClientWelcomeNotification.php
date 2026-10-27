<?php

namespace App\Listeners\Backend;

use App\Events\Backend\ClientCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendClientWelcomeNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ClientCreated $event): void
    {
        $client = $event->client;
        $profile = $client->clientProfile;

        if ($profile && $profile->email_consent && $client->email) {
            // TODO: Dispatch Welcome Mailable
            Log::info("Welcome email queued for client: {$client->email}");
        }
    }
}
