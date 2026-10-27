<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // User Auth Data
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],

            // RBAC
            'role_id' => ['required', 'exists:roles,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],

            // Profile Data
            'is_active' => ['boolean'],
            'phone' => ['nullable', 'numeric', 'digits:10'],
            'bio' => ['nullable', 'string', 'max:1000'],

            // Relationships
            'services' => ['nullable', 'array'],
            'services.*' => ['exists:services,id'],
            'locations' => ['nullable', 'array'],
            'locations.*' => ['exists:locations,id'],

            // Scheduling Arrays
            'hours' => ['required', 'array', 'size:7'],
            'hours.*.is_day_off' => ['boolean'],
            'hours.*.start_time' => ['nullable', 'date_format:H:i'],
            'hours.*.end_time' => ['nullable', 'date_format:H:i', 'after:hours.*.start_time'],
            'hours.*.break_start_time' => ['nullable', 'date_format:H:i', 'after:hours.*.start_time'],
            'hours.*.break_end_time' => ['nullable', 'date_format:H:i', 'after:hours.*.break_start_time', 'before:hours.*.end_time'],

            // Blackout Dates
            'blackout_dates' => ['nullable', 'array'],
            'blackout_dates.*.date' => ['required', 'date', 'after_or_equal:today'],
            'blackout_dates.*.reason' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = auth()->user();
            if ($user->hasRole('owner')) {
                return;
            }

            $requestedPermissions = $this->input('permissions', []);
            $userPermissions = $user->getAllPermissions()->pluck('id')->toArray();

            $unauthorizedDelegation = array_diff($requestedPermissions, $userPermissions);

            if (! empty($unauthorizedDelegation)) {
                $validator->errors()->add('permissions', 'You cannot assign permissions you do not possess.');
            }
        });
    }
}
