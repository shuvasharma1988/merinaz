<?php

namespace App\Http\Requests\Backend;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Appointment::class);
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:users,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'booking_source' => ['required', 'in:admin,phone,walk_in'],
            'status' => ['required', 'in:pending,confirmed,checked_in,in_progress,completed,cancelled,no_show'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
            'staff_notes' => ['nullable', 'string', 'max:1000'],

            // Validating the array of multiple services
            'items' => ['required', 'array', 'min:1'],
            'items.*.service_id' => ['required', 'exists:services,id'],
            'items.*.staff_id' => ['required', 'exists:users,id'],
            'items.*.start_time' => ['required', 'date_format:H:i'],
        ];
    }
}
