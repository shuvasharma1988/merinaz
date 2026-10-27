<?php

namespace App\Enums;

enum PostStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Published = 'published';
    case Archived = 'archived';

    /**
     * Only published posts (whose published_at has passed) are publicly visible.
     */
    public function isPubliclyVisible(): bool
    {
        return $this === self::Published;
    }

    /**
     * Scheduled posts become visible once their publish moment arrives.
     */
    public function isPubliclyVisibleAt(?\DateTimeInterface $moment): bool
    {
        if ($moment === null) {
            return false;
        }

        if ($this === self::Published) {
            return true;
        }

        return $this === self::Scheduled && $moment <= new \DateTimeImmutable;
    }

    /**
     * The label shown in the admin interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Scheduled => 'Scheduled',
            self::Published => 'Published',
            self::Archived => 'Archived',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $status): string => $status->value,
            self::cases(),
        );
    }
}
