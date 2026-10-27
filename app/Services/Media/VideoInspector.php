<?php

namespace App\Services\Media;

/**
 * Minimal, dependency free reader for the ISO base media file format
 * (MP4 / MOV) container.
 *
 * It is used to enforce a maximum clip duration on uploaded gallery videos
 * without relying on an external binary such as ffmpeg. Only the boxes that
 * are required to read the presentation duration are inspected.
 */
class VideoInspector
{
    /**
     * Return the duration of an MP4/MOV file in seconds, or null when the
     * container cannot be understood.
     */
    public function durationInSeconds(string $absolutePath): ?float
    {
        if (! is_readable($absolutePath)) {
            return null;
        }

        $handle = fopen($absolutePath, 'rb');

        if ($handle === false) {
            return null;
        }

        try {
            $size = filesize($absolutePath);

            if ($size === false || $size < 16) {
                return null;
            }

            $movie = $this->findBox($handle, 'moov', 0, $size);

            if ($movie === null) {
                return null;
            }

            $header = $this->findBox($handle, 'mvhd', $movie['start'], $movie['end']);

            if ($header === null) {
                return null;
            }

            fseek($handle, $header['start']);
            $payload = fread($handle, $header['end'] - $header['start']);

            if ($payload === false || strlen($payload) < 20) {
                return null;
            }

            $version = ord($payload[0]);

            if ($version === 1) {
                if (strlen($payload) < 32) {
                    return null;
                }

                $timescale = unpack('N', substr($payload, 20, 4))[1];
                $duration = unpack('J', substr($payload, 24, 8))[1];
            } else {
                $timescale = unpack('N', substr($payload, 12, 4))[1];
                $duration = unpack('N', substr($payload, 16, 4))[1];
            }

            if ($timescale <= 0) {
                return null;
            }

            return round($duration / $timescale, 2);
        } finally {
            fclose($handle);
        }
    }

    /**
     * Determine whether the file starts with an ISO base media container.
     */
    public function isIsoBaseMedia(string $absolutePath): bool
    {
        if (! is_readable($absolutePath)) {
            return false;
        }

        $handle = fopen($absolutePath, 'rb');

        if ($handle === false) {
            return false;
        }

        try {
            $header = fread($handle, 12);

            if ($header === false || strlen($header) < 12) {
                return false;
            }

            return substr($header, 4, 4) === 'ftyp';
        } finally {
            fclose($handle);
        }
    }

    /**
     * Locate a box by its four character type inside the given byte range.
     *
     * @param  resource  $handle
     * @return array{start: int, end: int}|null
     */
    private function findBox($handle, string $type, int $start, int $end): ?array
    {
        $offset = $start;

        while ($offset + 8 <= $end) {
            fseek($handle, $offset);
            $header = fread($handle, 8);

            if ($header === false || strlen($header) < 8) {
                return null;
            }

            $size = unpack('N', substr($header, 0, 4))[1];
            $boxType = substr($header, 4, 4);
            $headerSize = 8;

            if ($size === 1) {
                $large = fread($handle, 8);

                if ($large === false || strlen($large) < 8) {
                    return null;
                }

                $size = unpack('J', $large)[1];
                $headerSize = 16;
            } elseif ($size === 0) {
                $size = $end - $offset;
            }

            if ($size < $headerSize) {
                return null;
            }

            $boxEnd = min($offset + $size, $end);

            if ($boxType === $type) {
                return ['start' => $offset + $headerSize, 'end' => $boxEnd];
            }

            if (in_array($boxType, ['moov', 'trak', 'mdia'], true)) {
                $nested = $this->findBox($handle, $type, $offset + $headerSize, $boxEnd);

                if ($nested !== null) {
                    return $nested;
                }
            }

            $offset += $size;
        }

        return null;
    }
}
