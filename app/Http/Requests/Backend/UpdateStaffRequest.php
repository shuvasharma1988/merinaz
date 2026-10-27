<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        $staff = $this->route('staff');

        return Gate::allows('update', $staff);
    }

    public function rules(): array
    {
        $staffId = $this->route('staff')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$staffId],
            'password' => ['nullable', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],

            // Profile fields
            'is_active' => ['boolean'],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],

            // Relationships
            'services' => ['nullable', 'array'],
            'services.*' => ['exists:services,id'],
            'locations' => ['nullable', 'array'],
            'locations.*' => ['exists:locations,id'],

            // Scheduling arrays
            'hours' => ['required', 'array', 'size:7'], // mon through sun
            'hours.*.is_day_off' => ['boolean'],
            'hours.*.start_time' => ['nullable', 'date_format:H:i'],
            'hours.*.end_time' => ['nullable', 'date_format:H:i', 'after:hours.*.start_time'],
            'hours.*.break_start_time' => ['nullable', 'date_format:H:i', 'after:hours.*.start_time'],
            'hours.*.break_end_time' => ['nullable', 'date_format:H:i', 'after:hours.*.break_start_time', 'before:hours.*.end_time'],

            // Blackout dates array
            'blackout_dates' => ['nullable', 'array'],
            'blackout_dates.*.date' => ['required', 'date', 'after_or_equal:today'],
            'blackout_dates.*.reason' => ['nullable', 'string', 'max:255'],
        ];
    }
}
