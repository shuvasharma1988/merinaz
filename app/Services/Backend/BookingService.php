<?php

namespace App\Services\Backend;

use App\Events\Backend\BookingModified;
use App\Models\Appointment;
use App\Models\Location;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    public function __construct(protected AvailabilityService $availabilityService) {}

    public function createBooking(array $data): Appointment
    {
        return DB::transaction(function () use ($data) {
            $location = Location::where('id', $data['location_id'])->where('is_active', true)->firstOrFail();

            $appointment = Appointment::create([
                'location_id' => $location->id,
                'booking_reference' => strtoupper(Str::random(8)),
                'client_id' => $data['client_id'],
                'appointment_date' => $data['appointment_date'],
                'status' => $data['status'] ?? 'confirmed',
                'booking_source' => $data['booking_source'] ?? 'admin',
                'discount' => $data['discount'] ?? 0.00,
                'tax' => $data['tax'] ?? 0.00,
                'subtotal' => 0, // Computed below
                'total' => 0, // Computed below
                'customer_notes' => $data['customer_notes'] ?? null,
                'staff_notes' => $data['staff_notes'] ?? null,
            ]);

            $subtotal = 0;

            foreach ($data['items'] as $item) {
                $service = Service::findOrFail($item['service_id']);
                $duration = (int) ($service->duration ?? $service->duration_minutes ?? 30);
                $endTime = $this->availabilityService->calculateEndTime($item['start_time'], $duration);

                // This will throw descriptive exceptions if anything fails
                $this->availabilityService->verifySlotAvailability(
                    $location->id,
                    $item['staff_id'],
                    $service->id,
                    $data['appointment_date'],
                    $item['start_time'],
                    $endTime
                );

                $appointment->items()->create([
                    'service_id' => $service->id,
                    'staff_id' => $item['staff_id'],
                    'start_time' => $item['start_time'],
                    'end_time' => $endTime,
                    'price' => $service->price,
                ]);

                $subtotal += $service->price;
            }

            $total = ($subtotal - $appointment->discount) + $appointment->tax;
            $appointment->update([
                'subtotal' => $subtotal,
                'total' => max(0, $total),
            ]);

            event(new BookingModified($appointment, 'created'));

            return $appointment;
        });
    }

    public function updateBooking(Appointment $appointment, array $data): Appointment
    {
        return DB::transaction(function () use ($appointment, $data) {
            $locationId = $data['location_id'] ?? $appointment->location_id;

            // Delete old items to fully rebuild the multi-service list cleanly
            $appointment->items()->delete();

            $subtotal = 0;

            foreach ($data['items'] as $item) {
                $service = Service::findOrFail($item['service_id']);
                $duration = (int) ($service->duration ?? $service->duration_minutes ?? 30);
                $endTime = $this->availabilityService->calculateEndTime($item['start_time'], $duration);

                $this->availabilityService->verifySlotAvailability(
                    $locationId,
                    $item['staff_id'],
                    $service->id,
                    $data['appointment_date'],
                    $item['start_time'],
                    $endTime,
                    $appointment->id
                );

                $appointment->items()->create([
                    'service_id' => $service->id,
                    'staff_id' => $item['staff_id'],
                    'start_time' => $item['start_time'],
                    'end_time' => $endTime,
                    'price' => $service->price,
                ]);

                $subtotal += $service->price;
            }

            $discount = $data['discount'] ?? $appointment->discount;
            $tax = $data['tax'] ?? $appointment->tax;
            $total = ($subtotal - $discount) + $tax;

            $appointment->update([
                'location_id' => $locationId,
                'appointment_date' => $data['appointment_date'],
                'discount' => $discount,
                'tax' => $tax,
                'subtotal' => $subtotal,
                'total' => max(0, $total),
                'customer_notes' => $data['customer_notes'] ?? $appointment->customer_notes,
                'staff_notes' => $data['staff_notes'] ?? $appointment->staff_notes,
            ]);

            event(new BookingModified($appointment, 'updated'));

            return $appointment;
        });
    }
}
