<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = $this->staticUrls()
            ->concat($this->serviceUrls())
            ->concat($this->postUrls())
            ->concat($this->galleryUrls());

        $xml = view('frontend.sitemap', ['urls' => $urls])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: string|null, changefreq: string, priority: string}>
     */
    private function staticUrls(): Collection
    {
        $now = now()->toAtomString();

        return collect([
            route('home'),
            route('services.index'),
            route('gallery.index'),
            route('faq.index'),
            route('blog.index'),
            route('about'),
            route('contact'),
        ])->map(fn (string $url): array => [
            'loc' => $url,
            'lastmod' => $now,
            'changefreq' => 'weekly',
            'priority' => '0.7',
        ])->values();
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: string|null, changefreq: string, priority: string}>
     */
    private function serviceUrls(): Collection
    {
        return Service::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get()
            ->map(fn (Service $service): array => [
                'loc' => route('services.show', $service),
                'lastmod' => $service->updated_at?->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ])->values();
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: string|null, changefreq: string, priority: string}>
     */
    private function postUrls(): Collection
    {
        return Post::query()
            ->published()
            ->orderBy('id')
            ->get()
            ->map(fn (Post $post): array => [
                'loc' => route('blog.show', $post),
                'lastmod' => $post->updated_at?->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ])->values();
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: string|null, changefreq: string, priority: string}>
     */
    private function galleryUrls(): Collection
    {
        return GalleryItem::query()
            ->published()
            ->orderBy('id')
            ->get()
            ->map(fn (GalleryItem $item): array => [
                'loc' => route('gallery.show', $item),
                'lastmod' => $item->updated_at?->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ])->values();
    }
}
