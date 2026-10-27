<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreFaqRequest;
use App\Http\Requests\Backend\UpdateFaqRequest;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Service;
use App\Services\CategoryResolver;
use App\Services\Media\HtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function __construct(
        private readonly HtmlSanitizer $sanitizer,
        private readonly CategoryResolver $categories,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Faq::class);

        $faqs = Faq::query()
            ->with(['category', 'services'])
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->when($request->boolean('unpublished'), fn ($query) => $query->where('is_published', false))
            ->when($request->filled('search'), fn ($query) => $query->where('question', 'like', '%'.$request->string('search')->toString().'%'))
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        $categories = FaqCategory::query()->ordered()->get();

        return view('admin.faq.index', compact('faqs', 'categories'));
    }

    public function create(): View
    {
        Gate::authorize('create', Faq::class);

        $categories = FaqCategory::query()->ordered()->get();
        $services = Service::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.faq.create', compact('categories', 'services'));
    }

    public function store(StoreFaqRequest $request): RedirectResponse
    {
        Gate::authorize('create', Faq::class);

        $data = $request->validated();

        $faq = new Faq([
            'question' => $data['question'],
            'answer' => $this->sanitizer->sanitize($data['answer']),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_published' => (bool) ($data['is_published'] ?? false),
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        $faq->category_id = $this->categories->resolve(
            $data['category_id'] ?? null,
            $data['new_category'] ?? null,
            FaqCategory::class,
        );

        $faq->save();

        $faq->services()->sync($data['service_ids'] ?? []);

        return redirect()
            ->route('admin.faq.index')
            ->with('success', 'FAQ entry created successfully.');
    }

    public function show(Faq $faq): View
    {
        Gate::authorize('view', $faq);

        $faq->load(['category', 'services']);

        return view('admin.faq.details', compact('faq'));
    }

    public function edit(Faq $faq): View
    {
        Gate::authorize('update', $faq);

        $categories = FaqCategory::query()->ordered()->get();
        $services = Service::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.faq.edit', compact('faq', 'categories', 'services'));
    }

    public function update(UpdateFaqRequest $request, Faq $faq): RedirectResponse
    {
        Gate::authorize('update', $faq);

        $data = $request->validated();

        $faq->fill([
            'question' => $data['question'],
            'answer' => $this->sanitizer->sanitize($data['answer']),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_published' => (bool) ($data['is_published'] ?? false),
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'updated_by' => $request->user()->id,
        ]);

        $faq->category_id = $this->categories->resolve(
            $data['category_id'] ?? null,
            $data['new_category'] ?? null,
            FaqCategory::class,
        );

        $faq->save();

        $faq->services()->sync($data['service_ids'] ?? []);

        return redirect()
            ->route('admin.faq.index')
            ->with('success', 'FAQ entry updated successfully.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        Gate::authorize('delete', $faq);

        $faq->services()->detach();
        $faq->delete();

        return redirect()
            ->route('admin.faq.index')
            ->with('success', 'FAQ entry deleted successfully.');
    }
}
