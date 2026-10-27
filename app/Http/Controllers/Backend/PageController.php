<?php

namespace App\Http\Controllers\Backend;

use App\Events\Backend\PageModified;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StorePageRequest;
use App\Http\Requests\Backend\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Page::class);

        $pages = Page::orderByDesc('is_system')
            ->orderBy('title')
            ->paginate(15);

        return view('admin.pages.index', compact('pages'));
    }

    public function create(): View
    {
        Gate::authorize('create', Page::class);

        return view('admin.pages.create');
    }

    public function store(StorePageRequest $request): RedirectResponse
    {
        Gate::authorize('create', Page::class);

        $validated = $request->validated();

        $page = Page::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'content' => $validated['body'],
            'is_published' => true,
            'is_system' => in_array($validated['slug'], Page::PROTECTED_SLUGS, true),
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        event(new PageModified($request->user(), $page, 'created'));

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Static page created successfully.');
    }

    public function show(Page $page): View
    {
        Gate::authorize('view', $page);

        return view('admin.pages.show', compact('page'));
    }

    public function edit(Page $page): View
    {
        Gate::authorize('update', $page);

        return view('admin.pages.edit', compact('page'));
    }

    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        Gate::authorize('update', $page);

        $validated = $request->validated();

        $updateData = [
            'title' => $validated['title'],
            'content' => $validated['body'],
            'updated_by' => $request->user()->id,
        ];

        if (! $page->isProtectedSlug() && ! empty($validated['slug'])) {
            $updateData['slug'] = $validated['slug'];
        }

        $page->update($updateData);

        event(new PageModified($request->user(), $page, 'updated'));

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        if ($page->isProtectedSlug()) {
            return redirect()
                ->route('admin.pages.index')
                ->with('error', 'Protected system pages cannot be deleted.');
        }

        Gate::authorize('delete', $page);

        $actor = auth()->user();
        $page->delete();

        event(new PageModified($actor, $page, 'deleted'));

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }
}
