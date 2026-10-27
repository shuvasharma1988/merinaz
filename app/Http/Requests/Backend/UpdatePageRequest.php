<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('title') && ! $this->filled('slug')) {
            $this->merge([
                'slug' => Str::slug($this->input('title')),
            ]);
        }

        // Map body <-> content so both work regardless of controller version
        if ($this->filled('body') && ! $this->filled('content')) {
            $this->merge([
                'content' => $this->input('body'),
            ]);
        }
    }

    public function rules(): array
    {
        $page = $this->route('page');
        $pageId = is_object($page) ? $page->id : $page;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('pages', 'slug')->ignore($pageId),
            ],
            'body' => ['required', 'string'],
        ];
    }
}
