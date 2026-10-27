<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class ReplyContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reply_message' => ['required', 'string', 'min:5'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ];
    }
}
