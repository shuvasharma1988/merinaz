<?php

namespace App\Listeners\Backend;

use App\Events\Backend\BookingModified;
use Illuminate\Support\Facades\Log;

class LogBookingActivity
{
    public function handle(BookingModified $event): void
    {
        $user = auth()->user() ? auth()->user()->name : 'System';
        $appointment = $event->appointment;

        Log::info("Booking {$event->action} by {$user}", [
            'booking_id' => $appointment->id,
            'reference' => $appointment->booking_reference,
            'status' => $appointment->status,
        ]);

        // This is where you would hook into your existing database activity log table
        // ActivityLog::create([...])
    }
}
