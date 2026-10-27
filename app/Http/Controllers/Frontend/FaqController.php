<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $faqs = Faq::query()
            ->published()
            ->with('category')
            ->ordered()
            ->get();

        return view('frontend.faq.index', [
            'groups' => $this->group($faqs),
        ]);
    }

    /**
     * Group the published questions by their category.
     *
     * Questions that were saved without a category are collected in a
     * "General" group so that every published question stays reachable.
     *
     * @param  Collection<int, Faq>  $faqs
     * @return Collection<int, array{name: string, slug: string, faqs: Collection<int, Faq>}>
     */
    private function group(Collection $faqs): Collection
    {
        return $faqs
            ->groupBy(fn (Faq $faq) => $faq->category_id === null ? 'general' : (string) $faq->category_id)
            ->map(function (Collection $items): array {
                $category = $items->first()->category;

                return [
                    'name' => $category?->name ?? 'General',
                    'slug' => $category?->slug ?? 'general',
                    'faqs' => $items->values(),
                ];
            })
            ->values();
    }
}
