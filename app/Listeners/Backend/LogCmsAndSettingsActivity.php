<?php

namespace App\Listeners\Backend;

use App\Events\Backend\ContactMessageReplied;
use App\Events\Backend\PageModified;
use App\Events\Backend\SettingsUpdated;
use Illuminate\Support\Facades\Log;

class LogCmsAndSettingsActivity
{
    public function handle(object $event): void
    {
        if ($event instanceof SettingsUpdated) {
            Log::info('Site Settings Updated', [
                'actor_id' => $event->actor->id,
                'actor_email' => $event->actor->email,
                'updated_keys' => $event->updatedKeys,
            ]);
        } elseif ($event instanceof PageModified) {
            Log::info("CMS Page {$event->action}", [
                'actor_id' => $event->actor->id,
                'actor_email' => $event->actor->email,
                'page_id' => $event->page->id,
                'page_slug' => $event->page->slug,
                'action' => $event->action,
            ]);
        } elseif ($event instanceof ContactMessageReplied) {
            Log::info('Contact Message Replied', [
                'actor_id' => $event->actor->id,
                'actor_email' => $event->actor->email,
                'message_id' => $event->contactMessage->id,
                'recipient_email' => $event->contactMessage->email,
            ]);
        }
    }
}
