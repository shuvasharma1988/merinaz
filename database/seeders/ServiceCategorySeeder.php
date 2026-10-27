<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Waxing', 'description' => 'Professional waxing and hair removal services for women covering bikini, Brazilian, body, arms, legs, back, chest, and full body areas.'],
            ['name' => 'Hard Wax', 'description' => 'Professional hard wax hair removal services for women and men covering all body areas with standard and multi-pack pricing for consistent and smooth results.'],
            ['name' => 'Sugaring', 'description' => 'Expert sugaring hair removal services for facial areas, women, and men covering full body, intimate, arms, legs, back, and chest areas.'],
            ['name' => 'Threading', 'description' => 'Professional threading services for face, brows, and body including brow lamination, tinting add-ons, and multi-pack pricing.'],
            ['name' => 'Tinting', 'description' => 'Semi-permanent eyebrow and facial tinting services offered as standalone or combined add-on treatments for defined and colored brow results.'],
            ['name' => 'Trimming', 'description' => 'Personalized body grooming and trimming services for women and men covering all body areas.'],
            ['name' => 'Facial', 'description' => 'Professional facial treatments including HydraFacial, microdermabrasion, and basic facials for skin cleansing, rejuvenation, and tone improvement.'],
            ['name' => 'Eyelash Extensions', 'description' => 'Professional eyelash extension services including lifting, tinting, natural, classic, hybrid, and volume lash sets with refill pricing.'],
        ];

        foreach ($categories as $index => $category) {
            ServiceCategory::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}
