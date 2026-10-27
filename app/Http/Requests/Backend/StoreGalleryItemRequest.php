<?php

namespace App\Http\Requests\Backend;

use App\Enums\GalleryMediaType;
use App\Services\Media\UploadService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreGalleryItemRequest extends FormRequest
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
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $imageRules = ['nullable', 'file', 'mimetypes:'.implode(',', UploadService::IMAGE_MIME_TYPES), 'max:'.UploadService::MAX_IMAGE_SIZE_KB];
        $videoRules = ['nullable', 'file', 'mimetypes:'.implode(',', UploadService::VIDEO_MIME_TYPES), 'max:'.UploadService::MAX_VIDEO_SIZE_KB];
        $isVideo = $this->input('media_type') === GalleryMediaType::Video->value;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('gallery_items', 'slug')],
            'category_id' => ['nullable', 'integer', Rule::exists('gallery_categories', 'id')],
            'new_category' => ['nullable', 'string', 'max:100', Rule::unique('gallery_categories', 'name')],
            'media_type' => ['required', Rule::in(GalleryMediaType::options())],
            'image' => $isVideo
                ? $imageRules
                : ['required', 'file', 'mimetypes:'.implode(',', UploadService::IMAGE_MIME_TYPES), 'max:'.UploadService::MAX_IMAGE_SIZE_KB],
            'video' => $isVideo
                ? ['required', 'file', 'mimetypes:'.implode(',', UploadService::VIDEO_MIME_TYPES), 'max:'.UploadService::MAX_VIDEO_SIZE_KB]
                : $videoRules,
            'thumbnail' => $isVideo
                ? ['required', 'file', 'mimetypes:'.implode(',', UploadService::IMAGE_MIME_TYPES), 'max:'.UploadService::MAX_IMAGE_SIZE_KB]
                : ['nullable', 'file', 'mimetypes:'.implode(',', UploadService::IMAGE_MIME_TYPES), 'max:'.UploadService::MAX_IMAGE_SIZE_KB],
            'alt_text' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'image.required' => 'An image is required for image gallery items.',
            'video.required' => 'A video file is required for video gallery items.',
            'thumbnail.required' => 'A poster image is required for video gallery items.',
            'new_category.unique' => 'A category with this name already exists.',
            'category_id.exists' => 'The selected category no longer exists.',
        ];
    }
}
