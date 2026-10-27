<?php

namespace App\Services\Backend;

use App\Models\AppointmentItem;
use App\Models\StaffBlackoutDate;
use App\Models\StaffProfile;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class AvailabilityService
{
    /**
     * Strictly verifies availability and throws descriptive exceptions upon failure.
     */
    public function verifySlotAvailability(int $locationId, int $staffId, int $serviceId, string $date, string $startTime, string $endTime, ?int $excludeAppointmentId = null): void
    {
        $profile = StaffProfile::where('user_id', $staffId)->first();
        if (! $profile) {
            throw new Exception('The selected user does not have a valid staff profile.');
        }

        // 1. Blackout Date Check
        $isBlackout = StaffBlackoutDate::where('staff_profile_id', $profile->id)
            ->where('date', $date)
            ->exists();
        if ($isBlackout) {
            throw new Exception('Staff member is unavailable on this date due to scheduled time off.');
        }

        // 2. Service Authorization Check
        $canPerform = DB::table('service_staff')
            ->where('staff_profile_id', $profile->id)
            ->where('service_id', $serviceId)
            ->exists();
        if (! $canPerform) {
            throw new Exception('The selected staff member is not authorized to perform this service.');
        }

        // 3. Location & Working Hours Check
        $dbDayOfWeek = strtolower(Carbon::parse($date)->shortEnglishDayOfWeek);
        $readableDayName = ucfirst(Carbon::parse($date)->englishDayOfWeek);

        // A. Check Location Pivot Table
        $assignedToLocation = DB::table('location_staff')
            ->where('staff_id', $staffId) // User ID
            ->where('location_id', $locationId)
            ->exists();

        if (! $assignedToLocation) {
            throw new Exception('Staff member is not assigned to this location.');
        }

        // B. Check Working Hours (Using profile ID and the 3-letter day)
        $workingHours = DB::table('staff_working_hours')
            ->where('staff_profile_id', $profile->id)
            ->where('day_of_week', $dbDayOfWeek)
            ->where('is_day_off', 0)
            ->first();

        if (! $workingHours) {
            throw new Exception("Staff member is not scheduled to work on {$readableDayName}s.");
        }

        if ($startTime < $workingHours->start_time || $endTime > $workingHours->end_time) {
            $formattedStart = Carbon::parse($workingHours->start_time)->format('g:i A');
            $formattedEnd = Carbon::parse($workingHours->end_time)->format('g:i A');
            throw new Exception("The requested time falls outside the staff member's working hours ({$formattedStart} to {$formattedEnd}).");
        }

        // 4. Overlap Check (Querying appointment_items instead of appointments)
        $query = AppointmentItem::where('staff_id', $staffId)
            ->whereHas('appointment', function ($q) use ($date) {
                $q->where('appointment_date', $date)
                    ->whereNotIn('status', ['cancelled', 'no_show']);
            })
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            });

        if ($excludeAppointmentId) {
            $query->where('appointment_id', '!=', $excludeAppointmentId);
        }

        if ($query->exists()) {
            throw new Exception('The staff member is already booked during this time slot.');
        }
    }

    public function calculateEndTime(string $startTime, int $durationMinutes): string
    {
        return Carbon::parse($startTime)->addMinutes($durationMinutes)->format('H:i:s');
    }
}
