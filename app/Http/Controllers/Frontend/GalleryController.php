<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\GalleryCategory;
use App\Models\GalleryItem;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        $items = GalleryItem::query()
            ->published()
            ->with('category')
            ->ordered()
            ->get();

        $categories = GalleryCategory::query()
            ->active()
            ->ordered()
            ->get();

        return view('frontend.gallery.index', compact('items', 'categories'));
    }

    public function show(GalleryItem $galleryItem): View
    {
        abort_unless($galleryItem->is_published, 404);

        $galleryItem->load('category');

        $related = GalleryItem::query()
            ->published()
            ->where('id', '!=', $galleryItem->id)
            ->when(
                $galleryItem->category_id !== null,
                fn ($query) => $query->where('category_id', $galleryItem->category_id),
            )
            ->ordered()
            ->take(3)
            ->get();

        return view('frontend.gallery.show', compact('galleryItem', 'related'));
    }
}
