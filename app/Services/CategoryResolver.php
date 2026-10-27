<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Resolves the category of a content record.
 *
 * Editors may either pick an existing category or type a new name, in which
 * case the category is created on the fly.
 */
class CategoryResolver
{
    /**
     * @param  class-string<Model>  $modelClass
     */
    public function resolve(?int $categoryId, ?string $newCategory, string $modelClass): ?int
    {
        $newCategory = $newCategory !== null ? trim($newCategory) : '';

        if ($newCategory === '') {
            return $categoryId;
        }

        /** @var Model|null $category */
        $category = $modelClass::query()->firstWhere('name', $newCategory);

        if ($category === null) {
            $category = $modelClass::query()->create([
                'name' => $newCategory,
                'slug' => $this->uniqueSlug($modelClass, $newCategory),
            ]);
        }

        return (int) $category->getKey();
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    private function uniqueSlug(string $modelClass, string $name): string
    {
        $base = Str::slug($name);

        if ($base === '') {
            $base = 'category';
        }

        $slug = $base;
        $suffix = 1;

        while ($modelClass::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.(++$suffix);
        }

        return $slug;
    }
}
