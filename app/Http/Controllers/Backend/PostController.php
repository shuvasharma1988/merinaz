<?php

namespace App\Http\Controllers\Backend;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StorePostRequest;
use App\Http\Requests\Backend\UpdatePostRequest;
use App\Models\BlogCategory;
use App\Models\Post;
use App\Services\CategoryResolver;
use App\Services\Media\HtmlSanitizer;
use App\Services\Media\UploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(
        private readonly UploadService $uploads,
        private readonly HtmlSanitizer $sanitizer,
        private readonly CategoryResolver $categories,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Post::class);

        $posts = Post::query()
            ->with(['author', 'category'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->when($request->filled('search'), fn ($query) => $query->where('title', 'like', '%'.$request->string('search')->toString().'%'))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $categories = BlogCategory::query()->ordered()->get();

        return view('admin.posts.index', compact('posts', 'categories'));
    }

    public function create(): View
    {
        Gate::authorize('create', Post::class);

        $categories = BlogCategory::query()->ordered()->get();

        return view('admin.posts.create', compact('categories'));
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        Gate::authorize('create', Post::class);

        $data = $request->validated();

        $post = new Post([
            'author_id' => $request->user()->id,
            'updated_by' => $request->user()->id,
            'title' => $data['title'],
            'slug' => $data['slug'] ?? Str::slug($data['title']),
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $this->sanitizer->sanitize($data['content']),
            'featured_image_alt' => $data['featured_image_alt'] ?? null,
            'status' => PostStatus::from($data['status']),
            'published_at' => $this->resolvePublishedAt($data),
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'canonical_url' => $data['canonical_url'] ?? null,
            'og_title' => $data['og_title'] ?? null,
            'og_description' => $data['og_description'] ?? null,
            'twitter_card' => $data['twitter_card'] ?? 'summary_large_image',
        ]);

        $post->category_id = $this->categories->resolve(
            $data['category_id'] ?? null,
            $data['new_category'] ?? null,
            BlogCategory::class,
        );

        $post->save();

        if ($request->hasFile('featured_image')) {
            $upload = $this->uploads->storeImage($request->file('featured_image'), 'articles', $post->slug);
            $post->featured_image = $upload->path;
            $post->save();
        }

        if ($request->hasFile('og_image')) {
            $upload = $this->uploads->storeImage($request->file('og_image'), 'articles/og', $post->slug);
            $post->og_image = $upload->path;
            $post->save();
        }

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Article created successfully.');
    }

    public function show(Post $post): View
    {
        Gate::authorize('view', $post);

        $post->load(['author', 'category']);

        return view('admin.posts.show', compact('post'));
    }

    public function edit(Post $post): View
    {
        Gate::authorize('update', $post);

        $categories = BlogCategory::query()->ordered()->get();

        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        Gate::authorize('update', $post);

        $data = $request->validated();

        $post->fill([
            'title' => $data['title'],
            'slug' => $data['slug'] ?? $post->slug,
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $this->sanitizer->sanitize($data['content']),
            'featured_image_alt' => $data['featured_image_alt'] ?? null,
            'status' => PostStatus::from($data['status']),
            'published_at' => $this->resolvePublishedAt($data),
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'canonical_url' => $data['canonical_url'] ?? null,
            'og_title' => $data['og_title'] ?? null,
            'og_description' => $data['og_description'] ?? null,
            'twitter_card' => $data['twitter_card'] ?? 'summary_large_image',
            'updated_by' => $request->user()->id,
        ]);

        $post->category_id = $this->categories->resolve(
            $data['category_id'] ?? null,
            $data['new_category'] ?? null,
            BlogCategory::class,
        );

        $post->save();

        if ($request->hasFile('featured_image')) {
            $upload = $this->uploads->storeImage($request->file('featured_image'), 'articles', $post->slug);
            $this->uploads->delete($post->featured_image);
            $post->featured_image = $upload->path;
        }

        if ($request->hasFile('og_image')) {
            $upload = $this->uploads->storeImage($request->file('og_image'), 'articles/og', $post->slug);
            $this->uploads->delete($post->og_image);
            $post->og_image = $upload->path;
        }

        $post->save();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Article updated successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);

        $this->uploads->delete($post->featured_image);
        $this->uploads->delete($post->og_image);

        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Article deleted successfully.');
    }

    /**
     * A published article always carries a publication date so that it can be
     * shown on the public site.
     *
     * @param  array<string, mixed>  $data
     */
    private function resolvePublishedAt(array $data): ?string
    {
        $status = PostStatus::from($data['status']);

        if (! empty($data['published_at'])) {
            return $data['published_at'];
        }

        return $status === PostStatus::Published ? now()->toDateTimeString() : null;
    }
}
