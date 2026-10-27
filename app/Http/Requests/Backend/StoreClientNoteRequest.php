<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:users,id'],
            'note' => ['required', 'string', 'min:3'],
            'visibility' => ['required', 'in:private,team,manager'],
        ];
    }
}
