<?php

namespace App\Http\Controllers\Backend;

use App\Enums\GalleryMediaType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreGalleryItemRequest;
use App\Http\Requests\Backend\UpdateGalleryItemRequest;
use App\Models\GalleryCategory;
use App\Models\GalleryItem;
use App\Services\CategoryResolver;
use App\Services\Media\UploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GalleryItemController extends Controller
{
    public function __construct(
        private readonly UploadService $uploads,
        private readonly CategoryResolver $categories,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', GalleryItem::class);

        $items = GalleryItem::query()
            ->with('category')
            ->when($request->filled('media_type'), fn ($query) => $query->where('media_type', $request->string('media_type')->toString()))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->when($request->boolean('unpublished'), fn ($query) => $query->where('is_published', false))
            ->when($request->filled('search'), fn ($query) => $query->where('title', 'like', '%'.$request->string('search')->toString().'%'))
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        $categories = GalleryCategory::query()->ordered()->get();

        return view('admin.gallery.index', compact('items', 'categories'));
    }

    public function create(): View
    {
        Gate::authorize('create', GalleryItem::class);

        $categories = GalleryCategory::query()->ordered()->get();

        return view('admin.gallery.create', compact('categories'));
    }

    public function store(StoreGalleryItemRequest $request): RedirectResponse
    {
        Gate::authorize('create', GalleryItem::class);

        $data = $request->validated();

        $item = new GalleryItem([
            'title' => $data['title'],
            'slug' => $data['slug'] ?? Str::slug($data['title']),
            'media_type' => GalleryMediaType::from($data['media_type']),
            'alt_text' => $data['alt_text'],
            'caption' => $data['caption'] ?? null,
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_published' => (bool) ($data['is_published'] ?? false),
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'canonical_url' => $data['canonical_url'] ?? null,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        $item->category_id = $this->categories->resolve(
            $data['category_id'] ?? null,
            $data['new_category'] ?? null,
            GalleryCategory::class,
        );

        $item->save();

        $this->storeMedia($request, $item);

        return redirect()
            ->route('admin.gallery.index')
            ->with('success', 'Gallery item created successfully.');
    }

    public function show(GalleryItem $galleryItem): View
    {
        Gate::authorize('view', $galleryItem);

        $galleryItem->load('category');

        return view('admin.gallery.show', compact('galleryItem'));
    }

    public function edit(GalleryItem $galleryItem): View
    {
        Gate::authorize('update', $galleryItem);

        $categories = GalleryCategory::query()->ordered()->get();

        return view('admin.gallery.edit', compact('galleryItem', 'categories'));
    }

    public function update(UpdateGalleryItemRequest $request, GalleryItem $galleryItem): RedirectResponse
    {
        Gate::authorize('update', $galleryItem);

        $data = $request->validated();

        $galleryItem->fill([
            'title' => $data['title'],
            'slug' => $data['slug'] ?? $galleryItem->slug,
            'media_type' => GalleryMediaType::from($data['media_type']),
            'alt_text' => $data['alt_text'],
            'caption' => $data['caption'] ?? null,
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_published' => (bool) ($data['is_published'] ?? false),
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'canonical_url' => $data['canonical_url'] ?? null,
            'updated_by' => $request->user()->id,
        ]);

        $galleryItem->category_id = $this->categories->resolve(
            $data['category_id'] ?? null,
            $data['new_category'] ?? null,
            GalleryCategory::class,
        );

        $galleryItem->save();

        $this->storeMedia($request, $galleryItem);

        return redirect()
            ->route('admin.gallery.index')
            ->with('success', 'Gallery item updated successfully.');
    }

    public function destroy(GalleryItem $galleryItem): RedirectResponse
    {
        Gate::authorize('delete', $galleryItem);

        $this->uploads->delete($galleryItem->image);
        $this->uploads->delete($galleryItem->video);
        $this->uploads->delete($galleryItem->thumbnail);

        $galleryItem->delete();

        return redirect()
            ->route('admin.gallery.index')
            ->with('success', 'Gallery item deleted successfully.');
    }

    /**
     * Persist the uploaded media for the given item and clean up the files
     * that were replaced.
     */
    private function storeMedia(StoreGalleryItemRequest|UpdateGalleryItemRequest $request, GalleryItem $item): void
    {
        if ($request->hasFile('image')) {
            $upload = $this->uploads->storeImage($request->file('image'), 'gallery', $item->slug);
            $this->uploads->delete($item->image);
            $item->image = $upload->path;

            if ($item->thumbnail === null) {
                $item->thumbnail = $upload->thumbnailPath;
            }
        }

        if ($request->hasFile('video')) {
            $upload = $this->uploads->storeVideo($request->file('video'), 'gallery/videos', $item->slug);
            $this->uploads->delete($item->video);
            $item->video = $upload->path;
            $item->duration_seconds = $upload->durationSeconds !== null
                ? (int) round($upload->durationSeconds)
                : $item->duration_seconds;
        }

        if ($request->hasFile('thumbnail')) {
            $upload = $this->uploads->storeImage($request->file('thumbnail'), 'gallery/posters', $item->slug);
            $this->uploads->delete($item->thumbnail);
            $item->thumbnail = $upload->path;
        }

        $item->save();
    }
}
