<?php

namespace App\Services\Media;

/**
 * The result of a successful upload.
 */
final class StoredUpload
{
    public function __construct(
        public readonly string $path,
        public readonly string $url,
        public readonly string $mimeType,
        public readonly int $size,
        public readonly string $originalName = '',
        public readonly ?int $width = null,
        public readonly ?int $height = null,
        public readonly ?string $thumbnailPath = null,
        public readonly ?string $thumbnailUrl = null,
        public readonly ?float $durationSeconds = null,
    ) {}

    public function toArray(): array
    {
        return [
            'path' => $this->path,
            'url' => $this->url,
            'mime_type' => $this->mimeType,
            'size' => $this->size,
            'original_name' => $this->originalName,
            'width' => $this->width,
            'height' => $this->height,
            'thumbnail_path' => $this->thumbnailPath,
            'thumbnail_url' => $this->thumbnailUrl,
            'duration_seconds' => $this->durationSeconds,
        ];
    }
}
