<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * The single entry point for every file that is uploaded through the admin
 * panel.
 *
 * Uploads are stored on the `uploads` disk, which lives inside
 * `storage/app/private/uploads` and therefore *outside* of the public web
 * root. Files are streamed to the browser through the controlled
 * `media.show` route instead of being served directly by the web server.
 */
class UploadService
{
    public const DISK = 'uploads';

    /**
     * Content types that are accepted for images.
     *
     * @var array<int, string>
     */
    public const IMAGE_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    /**
     * @var array<int, string>
     */
    public const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    public const MAX_IMAGE_SIZE_KB = 5120;

    /**
     * @var array<int, string>
     */
    public const VIDEO_MIME_TYPES = ['video/mp4', 'video/quicktime'];

    /**
     * @var array<int, string>
     */
    public const VIDEO_EXTENSIONS = ['mp4', 'm4v', 'mov'];

    public const MAX_VIDEO_SIZE_KB = 51200;

    public const MAX_VIDEO_DURATION_SECONDS = 120;

    public function __construct(
        private readonly ImageThumbnailer $thumbnailer,
        private readonly VideoInspector $videoInspector,
    ) {}

    /**
     * Validate and store an uploaded image.
     *
     * @throws ValidationException
     */
    public function storeImage(UploadedFile $file, string $directory, ?string $baseName = null, bool $withThumbnail = true): StoredUpload
    {
        $mimeType = (string) $file->getMimeType();

        $this->assertImageIsValid($file, $mimeType);

        $extension = $this->extensionFor($mimeType);
        $path = $this->store($file, $directory, $baseName, $extension);

        $dimensions = @getimagesize(Storage::disk(self::DISK)->path($path));

        $thumbnailPath = null;
        $thumbnailUrl = null;

        if ($withThumbnail) {
            $thumbnailPath = $this->storeThumbnail($path, $mimeType);
            $thumbnailUrl = $thumbnailPath !== null ? $this->url($thumbnailPath) : null;
        }

        return new StoredUpload(
            path: $path,
            url: $this->url($path),
            mimeType: $mimeType,
            size: (int) $file->getSize(),
            originalName: $file->getClientOriginalName(),
            width: $dimensions[0] ?? null,
            height: $dimensions[1] ?? null,
            thumbnailPath: $thumbnailPath,
            thumbnailUrl: $thumbnailUrl,
        );
    }

    /**
     * Validate and store an uploaded video.
     *
     * @throws ValidationException
     */
    public function storeVideo(UploadedFile $file, string $directory, ?string $baseName = null): StoredUpload
    {
        $mimeType = (string) $file->getMimeType();

        $this->assertVideoIsValid($file, $mimeType);

        $extension = $this->extensionFor($mimeType);
        $path = $this->store($file, $directory, $baseName, $extension);

        $duration = $this->videoInspector->durationInSeconds(Storage::disk(self::DISK)->path($path));

        return new StoredUpload(
            path: $path,
            url: $this->url($path),
            mimeType: $mimeType,
            size: (int) $file->getSize(),
            originalName: $file->getClientOriginalName(),
            durationSeconds: $duration,
        );
    }

    /**
     * Remove a stored file (and its thumbnail) from the uploads disk.
     */
    public function delete(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        Storage::disk(self::DISK)->delete($path);
    }

    /**
     * Remove a stored thumbnail from the uploads disk.
     */
    public function deleteThumbnail(?string $path): void
    {
        $this->delete($path);
    }

    /**
     * Build the public URL of a stored file.
     */
    public function url(string $path): string
    {
        return route('media.show', ['path' => $path]);
    }

    /**
     * @throws ValidationException
     */
    private function assertImageIsValid(UploadedFile $file, string $mimeType): void
    {
        if (! in_array($mimeType, self::IMAGE_MIME_TYPES, true)) {
            throw ValidationException::withMessages([
                'file' => 'The uploaded file is not a supported image (allowed: JPG, PNG, WebP, GIF).',
            ]);
        }

        if (@getimagesize($file->getRealPath()) === false) {
            throw ValidationException::withMessages([
                'file' => 'The uploaded file could not be read as an image.',
            ]);
        }
    }

    /**
     * @throws ValidationException
     */
    private function assertVideoIsValid(UploadedFile $file, string $mimeType): void
    {
        if (! in_array($mimeType, self::VIDEO_MIME_TYPES, true)) {
            throw ValidationException::withMessages([
                'file' => 'The uploaded file is not a supported video (allowed: MP4, MOV).',
            ]);
        }

        $absolutePath = $file->getRealPath();

        if ($absolutePath === false || ! $this->videoInspector->isIsoBaseMedia($absolutePath)) {
            throw ValidationException::withMessages([
                'file' => 'The uploaded file is not a readable MP4/MOV container.',
            ]);
        }

        $duration = $this->videoInspector->durationInSeconds($absolutePath);

        if ($duration !== null && $duration > self::MAX_VIDEO_DURATION_SECONDS) {
            throw ValidationException::withMessages([
                'file' => sprintf(
                    'Videos may not be longer than %d seconds (uploaded: %s seconds).',
                    self::MAX_VIDEO_DURATION_SECONDS,
                    (string) $duration,
                ),
            ]);
        }
    }

    private function store(UploadedFile $file, string $directory, ?string $baseName, string $extension): string
    {
        $filename = $this->filename($file, $baseName, $extension);
        $path = trim($directory, '/');

        $stored = $file->storeAs($path, $filename, [
            'disk' => self::DISK,
            'visibility' => 'private',
        ]);

        if ($stored === false) {
            throw ValidationException::withMessages([
                'file' => 'The file could not be stored. Please try again.',
            ]);
        }

        return $stored;
    }

    private function storeThumbnail(string $path, string $mimeType): ?string
    {
        $disk = Storage::disk(self::DISK);
        $absolutePath = $disk->path($path);

        $thumbnailPath = $this->thumbnailer->generate($absolutePath, $mimeType);

        if ($thumbnailPath === null) {
            return null;
        }

        $relative = trim(dirname($path), '/').'/thumbs/'.basename($thumbnailPath);

        if (! $disk->put($relative, (string) file_get_contents($thumbnailPath))) {
            return null;
        }

        @unlink($thumbnailPath);

        return $relative;
    }

    /**
     * Build a descriptive but collision free file name.
     */
    private function filename(UploadedFile $file, ?string $baseName, string $extension): string
    {
        $source = $baseName ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slug = Str::slug($source);

        if ($slug === '') {
            $slug = 'upload';
        }

        return Str::limit($slug, 60, '').'-'.Str::lower(Str::random(8)).'.'.$extension;
    }

    /**
     * Map a content type onto the file extension that will be written.
     */
    private function extensionFor(string $mimeType): string
    {
        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'video/mp4' => 'mp4',
            'video/quicktime' => 'mov',
            default => 'bin',
        };
    }
}
