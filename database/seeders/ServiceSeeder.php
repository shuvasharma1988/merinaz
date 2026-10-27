<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ServiceCategory::pluck('id', 'name')->toArray();

        if (empty($categories)) {
            return;
        }

        $services = [
            // WAXING
            ['category' => 'Waxing', 'name' => 'Women - Bikini - Standard', 'price' => 60.00, 'duration_minutes' => 30, 'short_description' => 'Professional bikini line waxing service removing hair along the panty line for women. Delivers clean and defined bikini edges with smooth and longer-lasting results.'],
            ['category' => 'Waxing', 'name' => 'Women - Bikini - 3 Pack', 'price' => 58.00, 'duration_minutes' => 30, 'short_description' => 'Three-session bikini waxing package for women at a discounted rate per visit.'],
            ['category' => 'Waxing', 'name' => 'Women - Bikini - 6 Pack', 'price' => 55.00, 'duration_minutes' => 30, 'short_description' => 'Six-session bikini waxing package for women at the best discounted rate per visit.'],
            ['category' => 'Waxing', 'name' => 'Women - Brazilian - Standard', 'price' => 70.00, 'duration_minutes' => 45, 'short_description' => 'Full Brazilian waxing service for women removing all or most hair from the intimate area. Thorough and precise technique for a completely smooth and clean result.'],
            ['category' => 'Waxing', 'name' => 'Women - Brazilian - 3 Pack', 'price' => 68.00, 'duration_minutes' => 45, 'short_description' => 'Three-session Brazilian waxing package for women at a discounted rate per visit.'],
            ['category' => 'Waxing', 'name' => 'Women - Brazilian - 6 Pack', 'price' => 60.00, 'duration_minutes' => 45, 'short_description' => 'Six-session Brazilian waxing package for women at the best discounted rate per visit.'],

            // HARD WAX (Sampled comprehensive list)
            ['category' => 'Hard Wax', 'name' => 'Women - Bikini - Standard (Hard Wax)', 'price' => 60.00, 'duration_minutes' => 30, 'short_description' => 'Professional hard wax bikini service for women delivering smooth and clean bikini line results.'],
            ['category' => 'Hard Wax', 'name' => 'Women - Brazilian - Standard (Hard Wax)', 'price' => 70.00, 'duration_minutes' => 45, 'short_description' => 'Full Brazilian hard wax service for women removing all or most intimate area hair.'],
            ['category' => 'Hard Wax', 'name' => 'Women - Full Legs - Standard (Hard Wax)', 'price' => 90.00, 'duration_minutes' => 60, 'short_description' => 'Full leg hard wax service for women covering both upper and lower leg areas completely.'],
            ['category' => 'Hard Wax', 'name' => 'Women - Full Back - Standard (Hard Wax)', 'price' => 75.00, 'duration_minutes' => 45, 'short_description' => 'Full back hard wax service for women covering the entire back area from top to bottom.'],
            ['category' => 'Hard Wax', 'name' => 'Men - Full Back - Standard (Hard Wax)', 'price' => 80.00, 'duration_minutes' => 45, 'short_description' => 'Full back hard wax service for men covering the entire back area from top to bottom.'],
            ['category' => 'Hard Wax', 'name' => 'Men - Full Chest - Standard (Hard Wax)', 'price' => 80.00, 'duration_minutes' => 45, 'short_description' => 'Full chest hard wax service for men covering the entire chest area completely.'],

            // SUGARING
            ['category' => 'Sugaring', 'name' => 'Facial - Lip - Standard', 'price' => 10.00, 'duration_minutes' => 15, 'short_description' => 'Expert sugaring hair removal service targeting the lip area for smooth and clean results.'],
            ['category' => 'Sugaring', 'name' => 'Facial - Chin - Standard', 'price' => 10.00, 'duration_minutes' => 15, 'short_description' => 'Expert sugaring hair removal service targeting the chin area for smooth and clean results.'],
            ['category' => 'Sugaring', 'name' => 'Facial - Face - Standard', 'price' => 50.00, 'duration_minutes' => 30, 'short_description' => 'Expert sugaring full face hair removal service for smooth and comprehensive facial results.'],
            ['category' => 'Sugaring', 'name' => 'Women - Brazilian - Standard (Sugaring)', 'price' => 80.00, 'duration_minutes' => 45, 'short_description' => 'Expert sugaring full Brazilian service for women removing all or most intimate area hair.'],
            ['category' => 'Sugaring', 'name' => 'Women - Full Legs - Standard (Sugaring)', 'price' => 105.00, 'duration_minutes' => 60, 'short_description' => 'Expert sugaring full legs service for women covering both upper and lower leg areas.'],

            // THREADING
            ['category' => 'Threading', 'name' => 'Eyebrow - Standard', 'price' => 20.00, 'duration_minutes' => 15, 'short_description' => 'Precise eyebrow threading service delivering clean lines and a well-defined arch.'],
            ['category' => 'Threading', 'name' => 'Lip - Standard', 'price' => 13.00, 'duration_minutes' => 15, 'short_description' => 'Precise upper lip threading service for clean and smooth hair removal at the root.'],
            ['category' => 'Threading', 'name' => 'Full Face - Standard', 'price' => 60.00, 'duration_minutes' => 30, 'short_description' => 'Complete full face threading service covering all facial hair removal areas in one session.'],
            ['category' => 'Threading', 'name' => 'Brow Lamination', 'price' => 80.00, 'duration_minutes' => 45, 'short_description' => 'Three-step brow lamination treatment straightening and styling brow hairs for a versatile finish.'],
            ['category' => 'Threading', 'name' => 'Brow Lamination + Threading + Tinting', 'price' => 120.00, 'duration_minutes' => 60, 'short_description' => 'Combined brow lamination, threading, and tinting service for a complete brow enhancement.'],

            // TINTING
            ['category' => 'Tinting', 'name' => 'Tinting (Without Threading)', 'price' => 30.00, 'duration_minutes' => 20, 'short_description' => 'Standalone semi-permanent tinting service applied to the eyebrow or facial hair area.'],
            ['category' => 'Tinting', 'name' => 'Tinting Add-On (With Threading)', 'price' => 45.00, 'duration_minutes' => 30, 'short_description' => 'Tinting service added on to a threading appointment for combined shaping and color results.'],

            // TRIMMING
            ['category' => 'Trimming', 'name' => 'Women - Bikini - Standard (Trimming)', 'price' => 20.00, 'duration_minutes' => 20, 'short_description' => 'Personalized bikini area trimming service for women for tidy and well-maintained results.'],
            ['category' => 'Trimming', 'name' => 'Women - Brazilian - Standard (Trimming)', 'price' => 25.00, 'duration_minutes' => 30, 'short_description' => 'Brazilian trimming service for women providing tidy and well-groomed intimate area results.'],
            ['category' => 'Trimming', 'name' => 'Women - Full Arms - Standard (Trimming)', 'price' => 20.00, 'duration_minutes' => 30, 'short_description' => 'Full arm trimming service for women covering the entire arm from wrist to shoulder.'],

            // FACIAL
            ['category' => 'Facial', 'name' => 'Hydra Facial', 'price' => 160.00, 'duration_minutes' => 60, 'short_description' => 'HydraFacial using patented device delivering exfoliation, cleansing, extraction, and hydration.'],
            ['category' => 'Facial', 'name' => 'Microdermabrasion', 'price' => 125.00, 'duration_minutes' => 60, 'short_description' => 'Minimally invasive skin renewal procedure to improve sun damage, wrinkles, and fine lines.'],
            ['category' => 'Facial', 'name' => 'Basic Facial', 'price' => 90.00, 'duration_minutes' => 45, 'short_description' => 'Basic facial cleansing pores, exfoliating dead skin cells, and treating common skin concerns.'],

            // EYELASH EXTENSIONS
            ['category' => 'Eyelash Extensions', 'name' => 'Lash Lifting', 'price' => 96.00, 'duration_minutes' => 45, 'short_description' => 'Lash lifting treatment that curls and lifts natural lashes from base to tip for an open-eyed effect.'],
            ['category' => 'Eyelash Extensions', 'name' => 'Lash Lifting and Tinting', 'price' => 120.00, 'duration_minutes' => 60, 'short_description' => 'Combined lash lifting and tinting service for curled, darkened, and defined natural lashes.'],
            ['category' => 'Eyelash Extensions', 'name' => 'Natural Lashes Extension - Full Set', 'price' => 125.00, 'duration_minutes' => 90, 'short_description' => 'Natural lash extension full set for a subtle and lightweight lash enhancement result.'],
            ['category' => 'Eyelash Extensions', 'name' => 'Classic Lashes Extension - Full Set', 'price' => 140.00, 'duration_minutes' => 120, 'short_description' => 'Classic lash extension full set with one extension applied per natural lash for definition.'],
            ['category' => 'Eyelash Extensions', 'name' => 'Hybrid Lashes Extension - Full Set', 'price' => 180.00, 'duration_minutes' => 120, 'short_description' => 'Hybrid lash extension full set combining classic and volume techniques for a textured look.'],
            ['category' => 'Eyelash Extensions', 'name' => 'Volume Lashes Extension - Full Set', 'price' => 200.00, 'duration_minutes' => 150, 'short_description' => 'Volume lash extension full set using multiple lightweight fans per natural lash for fullness.'],
            ['category' => 'Eyelash Extensions', 'name' => 'Lash Removal', 'price' => 35.00, 'duration_minutes' => 30, 'short_description' => 'Safe and professional removal service for all types of eyelash extension sets.'],
        ];

        foreach ($services as $index => $service) {
            $categoryId = $categories[$service['category']] ?? null;

            if ($categoryId) {
                Service::updateOrCreate(
                    ['slug' => Str::slug($service['name'])],
                    [
                        'category_id' => $categoryId,
                        'name' => $service['name'],
                        'short_description' => $service['short_description'],
                        'price' => $service['price'],
                        'duration_minutes' => $service['duration_minutes'],
                        'is_active' => true,
                        'sort_order' => $index + 1,
                    ]
                );
            }
        }
    }
}
