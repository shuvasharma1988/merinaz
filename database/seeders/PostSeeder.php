<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\BlogCategory;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // Get the author (owner or manager)
        $author = User::whereHas('roles', function($q) {
            $q->whereIn('slug', ['owner', 'manager']);
        })->first();

        // Get categories
        $hairRemovalCategory = BlogCategory::where('slug', 'hair-removal-guides')->first();
        $skincareCategory = BlogCategory::where('slug', 'skincare-tips')->first();
        $studioNewsCategory = BlogCategory::where('slug', 'studio-news')->first();

        $posts = [
            [
                'category_id' => $hairRemovalCategory?->id,
                'title' => 'The Ultimate Guide to Brazilian Waxing Aftercare',
                'excerpt' => 'A complete step-by-step guide to soothe your skin, prevent ingrown hairs, and maintain a smooth finish after your Brazilian wax appointment.',
                'content' => '<p>Getting a Brazilian wax is the best way to achieve long-lasting smoothness, but the real secret to flawless skin lies in the aftercare...</p><h2>1. Avoid Heat and Friction</h2><p>For the first 24 to 48 hours, avoid saunas, hot tubs, heavy workouts, and tight clothing. Your skin is sensitive and prone to irritation.</p><h2>2. Exfoliate Strategically</h2><p>Wait at least 48 to 72 hours before gently exfoliating the area. Use a gentle scrub or a chemical exfoliant with salicylic acid to prevent ingrown hairs.</p><h2>3. Moisturize Daily</h2><p>Keep the skin hydrated with a lightweight, non-comedogenic lotion or oil. Hydrated skin allows hair to grow through easily without curling back underneath the surface.</p>',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(10),
                'meta_title' => 'Brazilian Waxing Aftercare Guide | Merinaz',
                'meta_description' => 'Discover the best aftercare tips to prevent ingrown hairs and maintain smooth skin after your Brazilian wax at Merinaz in Washington, DC.',
            ],
            [
                'category_id' => $hairRemovalCategory?->id,
                'title' => 'Sugaring vs. Hard Wax: Which is Right for You?',
                'excerpt' => 'Not sure whether to book sugaring or a hard wax appointment? We break down the differences, benefits, and pain levels of each hair removal method.',
                'content' => '<p>When it comes to professional hair removal, both sugaring and hard wax offer fantastic, long-lasting results. But which one should you choose?</p><h2>What is Sugaring?</h2><p>Sugaring uses a natural paste made of sugar, lemon, and water. It is applied against the hair growth and flicked off in the direction of hair growth. It only adheres to the hair and dead skin cells, making it incredibly gentle for sensitive skin.</p><h2>What is Hard Wax?</h2><p>Hard wax is applied warm, adheres to the hair as it cools and hardens, and is removed without cloth strips. It is highly effective for coarse hair and larger body areas.</p><h2>The Verdict</h2><p>If you have highly sensitive skin or are prone to ingrown hairs, sugaring might be your best bet. If you are targeting coarse hair like the bikini area or underarms, hard wax offers incredibly clean and quick results.</p>',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(5),
                'meta_title' => 'Sugaring vs Hard Wax | Hair Removal Differences',
                'meta_description' => 'Learn the difference between sugaring and hard wax hair removal to decide which gentle, long-lasting method is best for your skin type.',
            ],
            [
                'category_id' => $skincareCategory?->id,
                'title' => '5 Reasons You Need a HydraFacial This Season',
                'excerpt' => 'Is your skin feeling dull or congested? Discover why the HydraFacial is our most requested treatment for an instant, glowing complexion without the downtime.',
                'content' => '<p>The changing seasons can wreak havoc on our skin, leading to dryness, congestion, and a lackluster complexion. Enter the HydraFacial.</p><h2>1. Deep Cleansing and Extraction</h2><p>The patented vortex technology painlessly vacuums out pores while simultaneously pushing in potent actives, which moisturize, brighten, plump, and protect the skin.</p><h2>2. Instant Results with No Downtime</h2><p>Unlike chemical peels or laser treatments, you can leave our salon and head straight to dinner with radiant, glowing skin.</p><h2>3. Customizable for Every Skin Type</h2><p>Whether you struggle with acne, hyperpigmentation, or fine lines, our estheticians tailor the super-serums to your specific needs.</p>',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(2),
                'meta_title' => 'Benefits of HydraFacial Treatments | Merinaz',
                'meta_description' => 'Discover the top 5 reasons why a professional HydraFacial treatment is the ultimate solution for glowing, clear, and hydrated skin.',
            ],
            [
                'category_id' => $studioNewsCategory?->id,
                'title' => 'Welcome to the New Merinaz Experience',
                'excerpt' => 'We have completely revamped our booking system and studio layout to serve you better. Read about what is new in Adams Morgan.',
                'content' => '<p>We are thrilled to welcome you to the upgraded Merinaz experience! Our team has been working hard behind the scenes to streamline our services and enhance your comfort.</p><p>We have launched a brand new online booking engine, making it easier than ever to secure your appointments with your favorite specialists. Thank you for your continued support, and we cannot wait to see you in the studio!</p>',
                'status' => 'draft',
                'published_at' => null,
                'meta_title' => 'New Studio Updates | Merinaz Salon',
                'meta_description' => 'Read about the latest updates, new booking system, and studio enhancements at Merinaz Salon in Adams Morgan.',
            ]
        ];

        foreach ($posts as $postData) {
            $postData['author_id'] = $author?->id;
            $postData['slug'] = Str::slug($postData['title']);

            Post::firstOrCreate(
                ['slug' => $postData['slug']],
                $postData
            );
        }
    }
}
