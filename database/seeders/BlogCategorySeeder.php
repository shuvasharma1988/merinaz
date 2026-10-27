<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\BlogCategory;
use Illuminate\Support\Str;

class BlogCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Hair Removal Guides', 'description' => 'Expert tips and comprehensive guides for waxing, sugaring, and threading procedures.'],
            ['name' => 'Skincare Tips', 'description' => 'Pre and post-care advice, facial routines, and skincare education to maintain a healthy glow.'],
            ['name' => 'Brow & Lash Care', 'description' => 'Everything you need to know about brow shaping, lamination, tinting, and lash extensions.'],
            ['name' => 'Studio News', 'description' => 'Updates, promotions, and announcements from the Merinaz team.'],
        ];

        foreach ($categories as $index => $category) {
            BlogCategory::firstOrCreate(
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
