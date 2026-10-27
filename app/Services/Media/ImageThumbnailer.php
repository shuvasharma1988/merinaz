<?php

namespace App\Services\Media;

/**
 * Generates a downscaled derivative of an uploaded image using GD.
 *
 * The service never touches the filesystem itself: it receives the absolute
 * path of the original file and returns the absolute path of the generated
 * thumbnail, leaving persistence to the upload pipeline.
 */
class ImageThumbnailer
{
    /**
     * Maximum dimensions (in pixels) of a generated gallery thumbnail.
     */
    public const MAX_WIDTH = 800;

    public const MAX_HEIGHT = 800;

    /**
     * File extension used for a generated thumbnail per image type.
     *
     * @var array<string, string>
     */
    private const EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    /**
     * Create a thumbnail for the given image file.
     *
     * Returns null when the image type cannot be thumbnailed (for example
     * animated GIFs) or when the GD extension is unavailable.
     */
    public function generate(string $absolutePath, string $mimeType, ?int $maxWidth = null, ?int $maxHeight = null): ?string
    {
        if (! function_exists('imagecreatetruecolor')) {
            return null;
        }

        $maxWidth ??= self::MAX_WIDTH;
        $maxHeight ??= self::MAX_HEIGHT;

        $type = $this->detectType($absolutePath, $mimeType);
        $source = $this->read($absolutePath, $type);

        if (! $source instanceof \GdImage) {
            return null;
        }

        try {
            $width = imagesx($source);
            $height = imagesy($source);

            [$newWidth, $newHeight] = $this->scale($width, $height, $maxWidth, $maxHeight);

            if ($newWidth === $width && $newHeight === $height) {
                return null;
            }

            $thumbnail = imagecreatetruecolor($newWidth, $newHeight);

            if ($type === 'image/png' || $type === 'image/webp' || $type === 'image/gif') {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
                $transparent = imagecolorallocatealpha($thumbnail, 0, 0, 0, 127);
                imagefilledrectangle($thumbnail, 0, 0, $newWidth, $newHeight, $transparent);
            }

            imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            $target = $this->temporaryPath($absolutePath, $type);

            $written = match ($type) {
                'image/jpeg' => imagejpeg($thumbnail, $target, 82),
                'image/png' => imagepng($thumbnail, $target, 6),
                'image/webp' => imagewebp($thumbnail, $target, 82),
                default => false,
            };

            imagedestroy($thumbnail);

            return $written ? $target : null;
        } finally {
            imagedestroy($source);
        }
    }

    /**
     * Detect the real image type of a file, falling back to the uploaded mime
     * type when the file cannot be inspected.
     */
    private function detectType(string $absolutePath, string $mimeType): string
    {
        $size = @getimagesize($absolutePath);

        $detected = $size['mime'] ?? null;

        return is_string($detected) && $detected !== '' ? $detected : $mimeType;
    }

    private function read(string $absolutePath, string $mimeType): ?\GdImage
    {
        $image = match ($mimeType) {
            'image/jpeg' => @imagecreatefromjpeg($absolutePath),
            'image/png' => @imagecreatefrompng($absolutePath),
            'image/webp' => @imagecreatefromwebp($absolutePath),
            'image/gif' => @imagecreatefromgif($absolutePath),
            default => null,
        };

        return $image instanceof \GdImage ? $image : null;
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function scale(int $width, int $height, int $maxWidth, int $maxHeight): array
    {
        $ratio = min($maxWidth / $width, $maxHeight / $height, 1);

        return [
            max(1, (int) round($width * $ratio)),
            max(1, (int) round($height * $ratio)),
        ];
    }

    private function temporaryPath(string $absolutePath, string $mimeType): string
    {
        $directory = dirname($absolutePath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $extension = self::EXTENSIONS[$mimeType] ?? pathinfo($absolutePath, PATHINFO_EXTENSION);

        return $directory.'/'.pathinfo($absolutePath, PATHINFO_FILENAME).'-thumb.'.$extension;
    }
}
