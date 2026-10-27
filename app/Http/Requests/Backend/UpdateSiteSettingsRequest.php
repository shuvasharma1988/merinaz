<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
            'business_hours' => ['required', 'array'],
            'business_hours.monday_friday' => ['required', 'string', 'max:100'],
            'business_hours.saturday' => ['required', 'string', 'max:100'],
            'business_hours.sunday' => ['required', 'string', 'max:100'],
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_tiktok' => ['nullable', 'url', 'max:255'],
            'cancellation_window_hours' => ['required', 'integer', 'min:0', 'max:168'],
            'deposit_percentage' => ['required', 'integer', 'min:0', 'max:100'],
        ];
    }
}
