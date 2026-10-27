<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Resolves stored media paths into browser reachable URLs.
 *
 * Uploads live on the private `uploads` disk and are streamed through the
 * `media.show` route, so a raw stored path is never a valid public URL.
 */
trait HasStoredMedia
{
    /**
     * Build a public URL for a stored media path.
     */
    public function mediaUrl(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        return route('media.show', ['path' => $path]);
    }
}
