<?php

namespace Database\Seeders;

use App\Enums\GalleryMediaType;
use App\Enums\PostStatus;
use App\Models\BlogCategory;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\GalleryCategory;
use App\Models\GalleryItem;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Seeds the editorial content: journal categories and articles, gallery
 * categories and items, plus the FAQ groups and their questions.
 *
 * Every record is matched on its slug / question so the seeder can be re-run
 * without duplicating content.
 */
class ContentSeeder extends Seeder
{

    /**
     * @var array<string, array<string, mixed>>
     */
    private const GALLERY_CATEGORIES = [
        'interior' => ['name' => 'Interior', 'sort_order' => 1],
        'brows-and-threading' => ['name' => 'Brows & Threading', 'sort_order' => 2],
        'skincare' => ['name' => 'Skincare', 'sort_order' => 3],
        'details' => ['name' => 'Details', 'sort_order' => 4],
    ];

    /**
     * @var array<string, array<string, mixed>>
     */
    private const FAQ_CATEGORIES = [
        'prep' => ['name' => 'Prep', 'sort_order' => 1],
        'hygiene' => ['name' => 'Hygiene', 'sort_order' => 2],
        'policies' => ['name' => 'Policies', 'sort_order' => 3],
    ];

    /**
     * @var array<int, array<string, mixed>>
     */
    private const GALLERY_ITEMS = [
        [
            'slug' => 'the-studio-at-18th-street',
            'title' => 'The studio at 18th Street',
            'category' => 'interior',
            'media_type' => 'image',
            'image' => 'https://images.unsplash.com/photo-1600948836101-f9ffda59d250?q=80&w=1200&auto=format&fit=crop',
            'alt_text' => 'Warm beige treatment room with eucalyptus branches and folded white towels',
            'caption' => 'Room one, just before opening.',
            'description' => '<p>Two quiet rooms, warm light and everything you need within reach — and nothing you do not.</p>',
            'sort_order' => 1,
        ],
        [
            'slug' => 'threaded-brow-detail',
            'title' => 'Threaded brow detail',
            'category' => 'brows-and-threading',
            'media_type' => 'image',
            'image' => 'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?q=80&w=1200&auto=format&fit=crop',
            'alt_text' => 'Precisely shaped eyebrows after threading',
            'caption' => 'A soft arch, mapped by hand.',
            'description' => '<p>Every shape is mapped first, then trimmed one hair at a time.</p>',
            'sort_order' => 2,
        ],
        [
            'slug' => 'signature-facial-finish',
            'title' => 'Signature facial finish',
            'category' => 'skincare',
            'media_type' => 'image',
            'image' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?q=80&w=1200&auto=format&fit=crop',
            'alt_text' => 'Relaxing facial with cooling mask and massage',
            'caption' => 'Post-facial glow, no filter needed.',
            'description' => '<p>Double cleanse, enzyme exfoliation, extractions only where needed, and a hydrating mask under the LED.</p>',
            'sort_order' => 3,
        ],
        [
            'slug' => 'single-use-tools-detail',
            'title' => 'Single-use tools, every time',
            'category' => 'details',
            'media_type' => 'image',
            'image' => 'https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?q=80&w=1200&auto=format&fit=crop',
            'alt_text' => 'Esthetician preparing warm hypoallergenic wax with fresh applicators',
            'caption' => 'Fresh stick, fresh gloves, every guest.',
            'description' => '<p>We never double-dip. Each application uses a fresh, single-use applicator.</p>',
            'sort_order' => 4,
        ],
    ];

    /**
     * Global FAQ entries, grouped by their FAQ category slug.
     *
     * @var array<string, array<int, array<string, string>>>
     */
    private const FAQS = [
        'prep' => [
            [
                'question' => 'How long should my hair be before a waxing appointment?',
                'answer' => '<p>About a quarter inch — roughly two to three weeks of growth. If your hair is shorter we can still do what we can, and we will offer a free touch-up within seven days.</p>',
            ],
            [
                'question' => 'Should I exfoliate before I come in?',
                'answer' => '<p>Yes — exfoliate 24 hours before your appointment and shower on the day. Clean, dry skin grips wax far better and reduces pinching.</p>',
            ],
            [
                'question' => 'Can I wax while pregnant or breastfeeding?',
                'answer' => '<p>Sugaring and threading are usually fine. For waxing, let us know and we will choose a lower-heat formula and take extra care with positioning.</p>',
            ],
        ],
        'hygiene' => [
            [
                'question' => 'Do you double-dip?',
                'answer' => '<p>Never. Every application uses a fresh, single-use applicator, and our tools are sterilised in an autoclave between guests.</p>',
            ],
            [
                'question' => 'What should I do if I have sensitive skin or a reaction?',
                'answer' => '<p>Tell us before we start so we can patch test and choose a gentler formula. If you react afterwards, we will adjust the next visit and check in with you.</p>',
            ],
            [
                'question' => 'Do you use fresh linens for every guest?',
                'answer' => '<p>Always. Towels and linens are changed for every guest and laundered between appointments, and treatment chairs are disinfected with barrier film.</p>',
            ],
        ],
        'policies' => [
            [
                'question' => 'What is your cancellation policy?',
                'answer' => '<p>Reschedule or cancel free of charge up to 12 hours before your appointment. Inside 12 hours, 50% of the price applies, and no-shows are charged in full.</p>',
            ],
            [
                'question' => 'Do you take walk-ins?',
                'answer' => '<p>Yes for threading when an esthetician is free. Waxing, sugaring and facials need a booking so we can give you the room and the time.</p>',
            ],
            [
                'question' => 'How do I reschedule my appointment?',
                'answer' => '<p>Use the link in your confirmation email up to 12 hours before, call the studio, or send us a message from the contact page.</p>',
            ],
        ],
    ];

    /**
     * Per-service questions, matched to the service slug.
     *
     * @var array<string, array<int, array<string, string>>>
     */
    private const SERVICE_FAQS = [
        'brazilian-wax' => [
            [
                'question' => 'Does a Brazilian wax hurt?',
                'answer' => '<p>First-timers usually describe it as a quick warmth — most say 4 out of 10. Hair grows back finer and visits get easier, and we use a breathing and stretch technique to minimise stinging.</p>',
            ],
            [
                'question' => 'How long should hair be for a Brazilian?',
                'answer' => '<p>About a quarter inch (two to three weeks of growth). Too short and we cannot grip it; we will do what we can and touch up free within seven days.</p>',
            ],
        ],
        'bikini-wax' => [
            [
                'question' => 'What is the difference between a bikini and a Brazilian wax?',
                'answer' => '<p>A bikini wax tidies the line outside the underwear line only. A Brazilian is everything, front to back.</p>',
            ],
            [
                'question' => 'Can I come in while menstruating?',
                'answer' => '<p>Yes — with a tampon or cup and a fresh wipe. Let us know so we can allow extra comfort time.</p>',
            ],
        ],
        'brow-threading' => [
            [
                'question' => 'Is threading better than waxing for brows?',
                'answer' => '<p>For most sensitive skin, yes. Threading uses no heat and no chemicals, so it causes less redness and gives more precision.</p>',
            ],
            [
                'question' => 'How often should I get my brows threaded?',
                'answer' => '<p>Every three to four weeks keeps the shape crisp. We include a free touch-up within seven days of your visit.</p>',
            ],
        ],
        'fullface-threading' => [
            [
                'question' => 'Will the hair grow back thicker?',
                'answer' => '<p>No — that is a myth. Regrowth is soft and fine, and it takes longer to return after threading than after shaving.</p>',
            ],
            [
                'question' => 'How long does the redness last?',
                'answer' => '<p>Light pinkness fades within one to two hours with our cooling mist and aloe. A cool compress helps sensitive skin.</p>',
            ],
        ],
        'sugaring-brazilian' => [
            [
                'question' => 'Should I choose sugar or wax?',
                'answer' => '<p>Sugar is gentler and removes hair in the direction of growth, which means less breakage. It is a good choice if wax irritates you.</p>',
            ],
            [
                'question' => 'How should I prepare for sugaring?',
                'answer' => '<p>Exfoliate 48 hours before and arrive with clean, dry skin and no lotions.</p>',
            ],
        ],
        'sugaring-legs' => [
            [
                'question' => 'How long does the smoothness last?',
                'answer' => '<p>Three to four weeks, and regrowth gets sparser the more often you come in.</p>',
            ],
            [
                'question' => 'Can I shave between appointments?',
                'answer' => '<p>We would rather you did not — shaving resets your progress. Book a half-leg touch-up instead.</p>',
            ],
        ],
        'signature-facial' => [
            [
                'question' => 'Is there downtime after a facial?',
                'answer' => '<p>No peeling. You leave glowing with a slight flush that fades within about an hour.</p>',
            ],
            [
                'question' => 'Can I combine a facial with waxing?',
                'answer' => '<p>Yes, but leave 48 hours between them — book the facial first and wax two days later to avoid sensitivity.</p>',
            ],
        ],
        'lash-tint' => [
            [
                'question' => 'Is a lash lift and tint safe?',
                'answer' => '<p>Yes. We use ophthalmologist-tested tints and shields protect your eyes throughout the treatment.</p>',
            ],
            [
                'question' => 'What is the aftercare?',
                'answer' => '<p>Keep your lashes dry for 24 hours and avoid oil-based cleansers on the lash line for the same period.</p>',
            ],
        ],
    ];

    public function run(): void
    {
        $galleryCategoryIds = $this->seedGalleryCategories();
        $faqCategoryIds = $this->seedFaqCategories();

        $this->seedGalleryItems($galleryCategoryIds);
        $this->seedFaqs($faqCategoryIds);
    }

    /**
     * @return array<string, int>
     */
    private function seedGalleryCategories(): array
    {
        $ids = [];

        foreach (self::GALLERY_CATEGORIES as $slug => $category) {
            $ids[$slug] = GalleryCategory::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $category['name'],
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                ]
            )->id;
        }

        return $ids;
    }

    /**
     * @return array<string, int>
     */
    private function seedFaqCategories(): array
    {
        $ids = [];

        foreach (self::FAQ_CATEGORIES as $slug => $category) {
            $ids[$slug] = FaqCategory::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $category['name'],
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                ]
            )->id;
        }

        return $ids;
    }

    /**
     * @param  array<string, int>  $categoryIds
     */
    private function seedGalleryItems(array $categoryIds): void
    {
        foreach (self::GALLERY_ITEMS as $item) {
            GalleryItem::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'category_id' => $categoryIds[$item['category']],
                    'title' => $item['title'],
                    'media_type' => GalleryMediaType::from($item['media_type']),
                    'image' => $item['image'],
                    'alt_text' => $item['alt_text'],
                    'caption' => $item['caption'],
                    'description' => $item['description'],
                    'sort_order' => $item['sort_order'],
                    'is_published' => true,
                    'meta_title' => $item['title'].' | Merinaz Gallery',
                    'meta_description' => $item['caption'],
                ]
            );
        }
    }

    /**
     * @param  array<string, int>  $categoryIds
     */
    private function seedFaqs(array $categoryIds): void
    {
        $sortOrder = 1;

        foreach (self::FAQS as $categorySlug => $faqs) {
            foreach ($faqs as $faq) {
                $this->upsertFaq($faq, $categoryIds[$categorySlug], $sortOrder);
                $sortOrder++;
            }
        }

        foreach (self::SERVICE_FAQS as $serviceSlug => $faqs) {
            $service = Service::query()->where('slug', $serviceSlug)->first();

            if ($service === null) {
                continue;
            }

            foreach ($faqs as $faq) {
                $record = $this->upsertFaq($faq, $categoryIds['prep'], $sortOrder);
                $record->services()->syncWithoutDetaching([$service->id]);
                $sortOrder++;
            }
        }
    }

    /**
     * @param  array<string, string>  $faq
     */
    private function upsertFaq(array $faq, int $categoryId, int $sortOrder): Faq
    {
        return Faq::updateOrCreate(
            ['question' => $faq['question']],
            [
                'category_id' => $categoryId,
                'answer' => $faq['answer'],
                'sort_order' => $sortOrder,
                'is_published' => true,
                'meta_title' => $faq['question'].' | Merinaz FAQ',
            ]
        );
    }
}
