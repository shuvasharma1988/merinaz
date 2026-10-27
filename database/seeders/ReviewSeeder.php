<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // Intelligently fetch relevant service IDs to map to the reviews
        $threadingServiceId = Service::where('name', 'like', '%Threading%')->value('id');
        $microdermabrasionServiceId = Service::where('name', 'like', '%Microdermabrasion%')->value('id');

        $reviews = [
            [
                'reviewer_name' => 'Darling Elberry',
                'rating' => 5,
                'service_id' => $threadingServiceId,
                'body' => 'I have been going to Merinaz for years to get my eyebrows threaded with Lupita and she does an amazing job! Customer service is top notch! There is no wait time! Fast, convenient and affordable! This is gem in the Adams Morgan neighborhood!',
                'published_at' => '2026-10-15 10:00:00',
            ],
            [
                'reviewer_name' => 'Ajoya McCullough',
                'rating' => 5,
                'service_id' => null, // No specific service mentioned
                'body' => 'Always friendly and very thorough!',
                'published_at' => '2026-10-14 10:00:00',
            ],
            [
                'reviewer_name' => 'keonai perry',
                'rating' => 5,
                'service_id' => $microdermabrasionServiceId,
                'body' => 'I received a microdermabrasion... She did an amazing job thank thank so much she took her time & knew exactly what my face needed! I will be bck & will be sending ppl to this PLACE',
                'published_at' => '2026-10-01 10:00:00',
            ],
            [
                'reviewer_name' => 'Julissa Contreras',
                'rating' => 5,
                'service_id' => $threadingServiceId,
                'body' => 'Lupita is very accommodating of my preferences when it comes to threading my eyebrows and upper lip. She takes her time and does everything very precisely.',
                'published_at' => '2026-09-28 10:00:00',
            ],
            [
                'reviewer_name' => 'Jgmb',
                'rating' => 5,
                'service_id' => $microdermabrasionServiceId,
                'body' => "Ruche & lipika are the best! I definitely recommend coming here. I was a bit late and they were able to accommodate which is rare for salons. I got a microdermabrasion facial and my skin looks great after leaving can't wait to see the glow in a few days.",
                'published_at' => '2026-09-10 10:00:00',
            ],
            [
                'reviewer_name' => 'Rachel Giannelli',
                'rating' => 5,
                'service_id' => $threadingServiceId,
                'body' => 'Lilly did an amazing job threading my eyebrows!',
                'published_at' => '2026-08-28 10:00:00',
            ],
        ];

        foreach ($reviews as $review) {
            Review::firstOrCreate(
                ['reviewer_name' => $review['reviewer_name']],
                $review
            );
        }
    }
}
