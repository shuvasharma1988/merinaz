<?php

namespace App\Services\Backend;

use App\Models\AppointmentItem;
use App\Models\StaffProfile;
use App\Models\StaffBlackoutDate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class AvailabilityService
{
    /**
     * The time added after an appointment to clean up before the next client.
     */
    const BUFFER_MINUTES = 10;

    /**
     * How often slots are offered on the grid (e.g., every 15, 20, or 30 mins).
     */
    const SLOT_INTERVAL_MINUTES = 20;

    /**
     * Generates an array of available time slots for the frontend booking UI.
     */
    public function getAvailableTimeSlots(int $locationId, int $staffId, int $serviceId, int $durationMinutes, string $date): array
    {
        $profile = \App\Models\StaffProfile::where('user_id', $staffId)->first();
        if (!$profile) return [];

        $isBlackout = \App\Models\StaffBlackoutDate::where('staff_profile_id', $profile->id)
            ->where('date', $date)->exists();
        if ($isBlackout) return [];

        $dayOfWeek = strtolower(Carbon::parse($date)->shortEnglishDayOfWeek);

        $workingHours = \Illuminate\Support\Facades\DB::table('staff_working_hours')
            ->where('staff_profile_id', $profile->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_day_off', 0)
            ->first();

        if (!$workingHours || !$workingHours->start_time || !$workingHours->end_time) {
            return [];
        }

        $existingAppointments = \App\Models\AppointmentItem::where('staff_id', $staffId)
            ->whereHas('appointment', function ($q) use ($date) {
                $q->where('appointment_date', $date)
                    ->whereNotIn('status', ['cancelled', 'no_show']);
            })->get(['start_time', 'end_time']);

        $availableSlots = [];
        $isToday = Carbon::parse($date)->isToday();
        $now = Carbon::now();

        // Safely parse times to prevent Carbon crashes
        $shiftStart = Carbon::parse($date . ' ' . Carbon::parse($workingHours->start_time)->format('H:i:s'));
        $shiftEnd = Carbon::parse($date . ' ' . Carbon::parse($workingHours->end_time)->format('H:i:s'));

        $breakStart = $workingHours->break_start_time ? Carbon::parse($date . ' ' . Carbon::parse($workingHours->break_start_time)->format('H:i:s')) : null;
        $breakEnd = $workingHours->break_end_time ? Carbon::parse($date . ' ' . Carbon::parse($workingHours->break_end_time)->format('H:i:s')) : null;

        $currentTime = $shiftStart->copy();

        // Generate slots every 30 minutes
        while ($currentTime->copy()->addMinutes($durationMinutes)->lte($shiftEnd)) {
            $proposedStart = $currentTime->copy();
            // Include service duration + 10 MINUTE BUFFER
            $proposedEnd = $currentTime->copy()->addMinutes($durationMinutes + 10);
            $isValid = true;

            if ($isToday && $proposedStart->copy()->subMinutes(15)->lt($now)) $isValid = false;
            if ($proposedEnd->gt($shiftEnd)) $isValid = false;

            // Check lunch break
            if ($isValid && $breakStart && $breakEnd) {
                if ($proposedStart->lt($breakEnd) && $proposedEnd->gt($breakStart)) {
                    $isValid = false;
                }
            }

            // Check existing appointments
            if ($isValid) {
                foreach ($existingAppointments as $appt) {
                    // Extract exact time string to prevent crashes on Eloquent date casts
                    $timeStrStart = Carbon::parse($appt->start_time)->format('H:i:s');
                    $timeStrEnd = Carbon::parse($appt->end_time)->format('H:i:s');

                    $bookedStart = Carbon::parse($date . ' ' . $timeStrStart);
                    $bookedEnd = Carbon::parse($date . ' ' . $timeStrEnd)->addMinutes(10); // buffer applied

                    if ($proposedStart->lt($bookedEnd) && $proposedEnd->gt($bookedStart)) {
                        $isValid = false;
                        break;
                    }
                }
            }

            // Output all slots, but mark blocked ones as unavailable
            $availableSlots[] = [
                'time' => $proposedStart->format('H:i'),
                'label' => $proposedStart->format('g:i A'),
                'is_available' => $isValid
            ];

            $currentTime->addMinutes(30); // Move to next 30-min block
        }

        return $availableSlots;
    }

    /**
     * Strictly verifies availability and throws descriptive exceptions upon backend submission.
     */
    public function verifySlotAvailability(int $locationId, int $staffId, int $serviceId, string $date, string $startTime, string $endTime, ?int $excludeAppointmentId = null): void
    {
        $profile = \App\Models\StaffProfile::where('user_id', $staffId)->first();
        if (!$profile) {
            throw new \Exception("The selected user does not have a valid staff profile.");
        }

        // 1. Blackout Date Check
        $isBlackout = \App\Models\StaffBlackoutDate::where('staff_profile_id', $profile->id)
            ->where('date', $date)
            ->exists();
        if ($isBlackout) {
            throw new \Exception("Staff member is unavailable on this date due to scheduled time off.");
        }

        // 2. Service Authorization Check
        $canPerform = \Illuminate\Support\Facades\DB::table('service_staff')
            ->where('staff_profile_id', $profile->id)
            ->where('service_id', $serviceId)
            ->exists();
        if (!$canPerform) {
            throw new \Exception("The selected staff member is not authorized to perform this service.");
        }

        // 3. Location & Working Hours Check
        $dbDayOfWeek = strtolower(\Carbon\Carbon::parse($date)->shortEnglishDayOfWeek);
        $readableDayName = ucfirst(\Carbon\Carbon::parse($date)->englishDayOfWeek);

        $assignedToLocation = \Illuminate\Support\Facades\DB::table('location_staff')
            ->where('staff_id', $staffId) // User ID
            ->where('location_id', $locationId)
            ->exists();

        if (!$assignedToLocation) {
            throw new \Exception("Staff member is not assigned to this location.");
        }

        $workingHours = \Illuminate\Support\Facades\DB::table('staff_working_hours')
            ->where('staff_profile_id', $profile->id)
            ->where('day_of_week', $dbDayOfWeek)
            ->where('is_day_off', 0)
            ->first();

        if (!$workingHours) {
            throw new \Exception("Staff member is not scheduled to work on {$readableDayName}s.");
        }

        // FIX: Compare seconds since midnight to avoid AM/PM date offset crashes
        $requestedStartSeconds = \Carbon\Carbon::parse($startTime)->secondsSinceMidnight();
        $requestedEndSeconds = \Carbon\Carbon::parse($endTime)->secondsSinceMidnight();

        $shiftStartSeconds = \Carbon\Carbon::parse($workingHours->start_time)->secondsSinceMidnight();
        $shiftEndSeconds = \Carbon\Carbon::parse($workingHours->end_time)->secondsSinceMidnight();

        if ($requestedStartSeconds < $shiftStartSeconds || $requestedEndSeconds > $shiftEndSeconds) {
            $formattedStart = \Carbon\Carbon::parse($workingHours->start_time)->format('g:i A');
            $formattedEnd = \Carbon\Carbon::parse($workingHours->end_time)->format('g:i A');
            throw new \Exception("The requested time falls outside the staff member's working hours ({$formattedStart} to {$formattedEnd}).");
        }

        // 4. Overlap Check (Accounting for the 10 minute buffer)
        $query = \App\Models\AppointmentItem::where('staff_id', $staffId)
            ->whereHas('appointment', function ($q) use ($date) {
                $q->where('appointment_date', $date)
                    ->whereNotIn('status', ['cancelled', 'no_show']);
            })
            ->where(function ($query) use ($startTime, $endTime) {
                // Apply buffer mathematically in the SQL query
                $bufferedStartTime = \Carbon\Carbon::parse($startTime)->subMinutes(self::BUFFER_MINUTES)->format('H:i:s');
                $bufferedEndTime = \Carbon\Carbon::parse($endTime)->addMinutes(self::BUFFER_MINUTES)->format('H:i:s');

                $query->where('start_time', '<', $bufferedEndTime)
                    ->where('end_time', '>', $bufferedStartTime);
            });

        if ($excludeAppointmentId) {
            $query->where('appointment_id', '!=', $excludeAppointmentId);
        }

        if ($query->exists()) {
            throw new \Exception("The staff member is already booked during this time slot.");
        }
    }

    public function calculateEndTime(string $startTime, int $durationMinutes): string
    {
        return Carbon::parse($startTime)->addMinutes($durationMinutes)->format('H:i:s');
    }
}
