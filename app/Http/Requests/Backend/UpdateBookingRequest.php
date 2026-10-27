<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('booking'));
    }

    public function rules(): array
    {
        return [
            'location_id' => ['sometimes', 'required', 'exists:locations,id'],
            'appointment_date' => ['required', 'date'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
            'staff_notes' => ['nullable', 'string', 'max:1000'],

            // Re-validating the updated array of services
            'items' => ['required', 'array', 'min:1'],
            'items.*.service_id' => ['required', 'exists:services,id'],
            'items.*.staff_id' => ['required', 'exists:users,id'],
            'items.*.start_time' => ['required', 'date_format:H:i'],
        ];
    }
}
