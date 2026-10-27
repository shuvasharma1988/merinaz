<?php

namespace App\Enums;

enum GalleryMediaType: string
{
    case Image = 'image';
    case Video = 'video';

    public function label(): string
    {
        return match ($this) {
            self::Image => 'Image',
            self::Video => 'Video',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $type): string => $type->value,
            self::cases(),
        );
    }
}
