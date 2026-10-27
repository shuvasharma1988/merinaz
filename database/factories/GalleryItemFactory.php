<?php

namespace Database\Factories;

use App\Enums\GalleryMediaType;
use App\Models\GalleryItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<GalleryItem>
 */
class GalleryItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'category_id' => null,
            'title' => Str::title($title),
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 9999),
            'media_type' => GalleryMediaType::Image,
            'image' => null,
            'video' => null,
            'thumbnail' => null,
            'alt_text' => fake()->sentence(4),
            'caption' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'duration_seconds' => null,
            'sort_order' => fake()->numberBetween(0, 20),
            'is_published' => false,
            'meta_title' => null,
            'meta_description' => null,
            'canonical_url' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }

    public function video(): static
    {
        return $this->state(fn (array $attributes) => [
            'media_type' => GalleryMediaType::Video,
            'image' => null,
            'duration_seconds' => fake()->numberBetween(5, 90),
        ]);
    }
}
