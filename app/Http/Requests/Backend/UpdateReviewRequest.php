<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Handled by Controller Gate
    }

    public function rules(): array
    {
        return [
            'reviewer_name' => ['required', 'string', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'service_id' => ['nullable', 'exists:services,id'],
            'body' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
