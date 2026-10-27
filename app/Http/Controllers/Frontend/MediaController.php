<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Media\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams files that live on the private `uploads` disk.
 *
 * Uploads are kept outside of the public web root, so every media file is
 * delivered through this controller with strict headers and HTTP range
 * support (required for video scrubbing).
 */
class MediaController extends Controller
{
    public function show(Request $request, string $path): StreamedResponse
    {
        $path = $this->normalise($path);

        if ($path === null) {
            abort(404);
        }

        $disk = Storage::disk(UploadService::DISK);

        if (! $disk->exists($path)) {
            abort(404);
        }

        $absolutePath = $disk->path($path);
        $size = (int) $disk->size($path);
        $mimeType = $disk->mimeType($path) ?: 'application/octet-stream';

        $response = $this->makeResponse($request, $absolutePath, $path, $mimeType, $size);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        $response->headers->set('Content-Disposition', 'inline; filename="'.basename($path).'"');

        return $response;
    }

    /**
     * Build a full or partial (206) response for the requested file.
     */
    private function makeResponse(Request $request, string $absolutePath, string $path, string $mimeType, int $size): StreamedResponse
    {
        $range = $request->header('Range');
        $start = 0;
        $end = max(0, $size - 1);
        $partial = false;

        if ($range !== null && preg_match('/bytes=(\d*)-(\d*)/', $range, $matches) === 1) {
            $partial = true;

            if ($matches[1] !== '') {
                $start = (int) $matches[1];
            }

            if ($matches[2] !== '') {
                $end = min((int) $matches[2], $size - 1);
            } elseif ($matches[1] === '') {
                // Suffix range: the last N bytes are requested.
                $start = max(0, $size - (int) $matches[2]);
            }

            if ($start > $end || $start >= $size) {
                $response = new StreamedResponse;
                $response->setStatusCode(416);
                $response->headers->set('Content-Range', 'bytes */'.$size);

                return $response;
            }
        }

        $length = $end - $start + 1;

        $response = new StreamedResponse;
        $response->headers->set('Content-Type', $mimeType);
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Content-Length', (string) $length);

        if ($partial) {
            $response->setStatusCode(206);
            $response->headers->set('Content-Range', 'bytes '.$start.'-'.$end.'/'.$size);
        }

        $response->setCallback(function () use ($absolutePath, $start, $length) {
            $handle = fopen($absolutePath, 'rb');

            if ($handle === false) {
                return;
            }

            fseek($handle, $start);

            $remaining = $length;
            $chunkSize = 1024 * 512;

            while ($remaining > 0 && ! feof($handle)) {
                $chunk = fread($handle, (int) min($chunkSize, $remaining));

                if ($chunk === false || $chunk === '') {
                    break;
                }

                echo $chunk;
                $remaining -= strlen($chunk);

                flush();
            }

            fclose($handle);
        });

        return $response;
    }

    /**
     * Reject anything that could escape the uploads directory.
     */
    private function normalise(string $path): ?string
    {
        $path = str_replace('\\', '/', trim($path));

        if ($path === '' || str_contains($path, "\0") || str_contains($path, '..')) {
            return null;
        }

        return ltrim($path, '/');
    }
}
