<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('owner');
    }

    protected function prepareForValidation()
    {
        if ($this->has('name')) {
            $this->merge(['slug' => Str::slug($this->name)]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'unique:locations,slug'],
            'address' => ['required', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['boolean'],
            'staff' => ['nullable', 'array'],
            'staff.*' => ['exists:users,id'],
        ];
    }
}
