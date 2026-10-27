<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreReviewRequest;
use App\Http\Requests\Backend\UpdateReviewRequest;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Review::class);

        $reviews = Review::with('service')
            ->orderByRaw('published_at IS NULL DESC') // Drafts first
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.review.index', compact('reviews'));
    }

    public function create(): View
    {
        Gate::authorize('create', Review::class);

        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('admin.review.create', compact('services'));
    }

    public function store(StoreReviewRequest $request): RedirectResponse
    {
        Gate::authorize('create', Review::class);

        Review::create($request->validated());

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', 'Review created successfully.');
    }

    public function show(Review $review): View
    {
        Gate::authorize('view', $review);

        $review->load('service');

        return view('admin.review.details', compact('review'));
    }

    public function edit(Review $review): View
    {
        Gate::authorize('update', $review);

        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('admin.review.edit', compact('review', 'services'));
    }

    public function update(UpdateReviewRequest $request, Review $review): RedirectResponse
    {
        Gate::authorize('update', $review);

        $review->update($request->validated());

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', 'Review updated successfully.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        Gate::authorize('delete', $review);

        $review->delete();

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }
}
